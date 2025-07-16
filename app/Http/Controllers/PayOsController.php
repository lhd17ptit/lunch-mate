<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayOS\ProcessPaymentRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Repositories\TransactionRepository;
use Exception;
use App\Services\PayOsService;

class PayOsController extends Controller
{
	protected $transactionRepo;
	protected $payOsService;

	public function __construct(
		TransactionRepository $transactionRepo,
		PayOsService $payOsService
	) {
		$this->transactionRepo = $transactionRepo;
		$this->payOsService = $payOsService;
	}

	public function index(Request $request)
	{
		return view('payos.sandbox');
	}

	public function process(ProcessPaymentRequest $request)
	{
		$response = $this->payOsService->processPayment($request);

		if ($request->ajax()) {
			return response()->json([
				'redirect_url' => $response
			]);
		}
		return redirect()->away($response);
	}

    public function testProcess(Request $request){
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $transactionReference = time();
        $inputData = [
            "amount" => (int)(($request->amount ?? 0)),
            "description" => $request->description ?? null,
            "orderCode" => $transactionReference,
            "returnUrl" => URL::route('payos.return', [], true),
            "cancelUrl" => URL::route('payos.return', [], true),
            "expiredAt" => now()->addMinutes(5)->timestamp,
        ];
        // $userId = auth()->id() ?? null;
        // $transactionData = [
        //     'order_id' => $transactionReference,
        //     'user_id' => $userId,
        //     'note' => $request->vnp_OrderInfo ?? null,
        //     'amount' => $request->vnp_Amount ?? 0,
        //     'guest_uid' => $userId ? null : bin2hex(random_bytes(16)),
        //     'raw_payload' => json_encode($inputData),
        // ];
        // try {
        //     $this->transactionRepo->create($transactionData);
        // } catch (Exception $e) {
        //     Log::error($e->getMessage());
        //     return false;
        // }

        $signatureKeys = [
            'amount',
            'cancelUrl',
            'description',
            'orderCode',
            'returnUrl',
        ];

        $signatureData = array_intersect_key($inputData, array_flip($signatureKeys));

        ksort($signatureData);

        // dd($signatureData);

        $signatureArray = [];
        foreach($signatureData as $key => $value){
            $signatureArray[] = $key . '=' . $value;
        }

        $signatureString = implode('&', $signatureArray);

        $signature = hash_hmac('sha256', $signatureString, env('PAYOS_CHECKSUM', ''));
        $inputData['signature'] = $signature;

        // dd($signatureString, $signature);

        $headers = [
            'x-client-id' => env('PAYOS_CLIENT_ID', ''),
            'x-api-key' => env('PAYOS_API_KEY', ''),
            'x-partner-code' => env('PAYOS_PARTNER_CODE', ''),
            // 'Content-Type' => 'application/json',
        ];
        // dd($signature);
        $response = Http::withHeaders($headers)->post(env('PAYOS_PAYMENT_REQUEST_URL', ''), $inputData);
        $responseJson = $response->json();

        $qRText = data_get($responseJson, 'data.qrCode');
        $image = Http::get(env('QUICKCHART_IO_QR_URL', ''),['text' => $qRText]);
        dd($image->body());
        dd($response->json(), $inputData, $signatureArray, $signatureString);
    
    }

	public function return(Request $request)
	{
		return view('payos.return', $request->all());
	}

	public function ipn(Request $request)
	{
		$returnData = $this->payOsService->handleIpn($request);
		return response()->json($returnData);
	}

    public function handleWebhook(Request $request){
        return $this->payOsService->handleWebhook($request->all());
    }

}
