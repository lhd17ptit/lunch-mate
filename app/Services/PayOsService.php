<?php

namespace App\Services;

use App\Http\Requests\PayOS\ProcessPaymentRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use App\Repositories\TransactionRepository;
use Exception;

class PayOsService
{
    protected $transactionRepo;

    public function __construct(TransactionRepository $transactionRepo)
    {
        $this->transactionRepo = $transactionRepo;
    }

    public function processPayment(ProcessPaymentRequest|Request $request)
    {
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
        
        // generate QR image
        // $qRText = data_get($responseJson, 'data.qrCode');
        // $image = Http::get(env('QUICKCHART_IO_QR_URL', ''),['text' => $qRText]);
        // return response($image->body(), 200)->header('Content-Type', 'image/png');

        // option return PayOS link
        $link = data_get($responseJson, 'data.checkoutUrl');
        return $link;
        
    }

    public function handleWebhook($data){
        $check = $this->validatePayload($data);
        if($check){
            // TODO update order/transaction

            return response()->json(['message' => 'Ok'], 200);
        }

        return response()->json(['message' => 'Mismatch signature'], 400);
    }

    private function validatePayload($data){
        $signatureData = $data['signature'] ?? null;
        $checkData = $data['data'] ?? [];
        ksort($checkData);
        $dataStringArr = [];
        foreach($checkData as $key => $value){
            if (in_array($value, ["undefined", "null"]) || gettype($value) == "NULL") {
                  $value = "";
              }

              if (is_array($value)) {
                  $valueSortedElementObj = array_map(function ($ele) {
                      ksort($ele);
                      return $ele;
                  }, $value);
                  $value = json_encode($valueSortedElementObj, JSON_UNESCAPED_UNICODE);
              }
              $dataStringArr[] = $key . "=" . $value;
        }

        $dataString = implode("&", $dataStringArr);
        $signature = hash_hmac("sha256", $dataString, env('PAYOS_CHECKSUM', ''));
        return $signature == $signatureData;
    }
} 