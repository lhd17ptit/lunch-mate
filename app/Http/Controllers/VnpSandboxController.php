<?php

namespace App\Http\Controllers;

use App\Http\Requests\VNP\ProcessPaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class VnpSandboxController extends Controller
{
    public function index(Request $request)
    {
        return view('vnpay.sandbox');
    }

    public function process(ProcessPaymentRequest $request)
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        // The params for payment (except vnp_SecureHash)
        $inputData = [
            "vnp_Amount" => $request->vnp_Amount * 100, // Convert to smallest currency unit
            "vnp_Command" => "pay",
            "vnp_CreateDate" => now()->format('YmdHis'),
            "vnp_ExpireDate" => now()->addMinutes(5)->format('YmdHis'),
            "vnp_CurrCode" => "VND",
            // "vnp_BankCode" => "MBAPP", // comment to let user choose payment method
            "vnp_IpAddr" => $request->ip(),
            "vnp_Locale" => config('app.locale', 'en') == 'vi' ? 'vn' : 'en',
            "vnp_OrderInfo" => $request->vnp_OrderInfo,
            "vnp_OrderType" => '100000', // https://sandbox.vnpayment.vn/apis/docs/loai-hang-hoa/
            "vnp_ReturnUrl" => URL::route('vnpay.return', [], true),
            "vnp_TmnCode" => config('constants.ENV.VNP_TMN_CODE'),
            "vnp_TxnRef" => time(),
            "vnp_Version" => "2.1.0"
        ];

        // Sort the array by keys
        ksort($inputData);

        $hashDataArr = [];
        $queryArr = [];

        foreach ($inputData as $key => $value) {
            $hashDataArr[] = urlencode($key) . '=' . urlencode($value);
            $queryArr[] = urlencode($key) . '=' . urlencode($value);
        }

        $hashData = implode('&', $hashDataArr);
        $query = implode('&', $queryArr);

        $vnpSecureHash = hash_hmac('sha512', $hashData, config('constants.ENV.VNP_HASH_SECRET'));

        // Final URL
        $vnp_Url = config('constants.ENV.VNP_URL') . "?" . $query . "&" . config('constants.VNP.FIELD.HASH') . "=" . $vnpSecureHash;

        if ($request->ajax()) {
            return response()->json([
                'redirect_url' => $vnp_Url
            ]);
        }

        return redirect()->away($vnp_Url);
    }

    public function return(Request $request)
    {
        // return page to notify user of transaction result
        $data = [
            'status' => $request[config('constants.VNP.FIELD.RESPONSE_CODE')] ?? null,
            'transaction_id' => $request[config('constants.VNP.FIELD.TRANSACTION_ID')] ?? null,
            'message' => $request[config('constants.VNP.FIELD.MESSAGE')] ?? null,
        ];
        $validateChecksum = $this->validateChecksum($request->all());
        if(!$validateChecksum){
            $data['status'] = config('constants.VNP.RESPONSE_CODE.INVALID_SIGNATURE');
        }

        return view('vnpay.return', $data);
    }

    public function ipn(Request $request)
    {
        // Log the raw request data
        Log::info('VNPay IPN - Raw Request', [
            'all_data' => $request->all(),
            'headers' => $request->headers->all(),
            'ip' => $request->ip(),
            'method' => $request->method(),
            'url' => $request->fullUrl()
        ]);

        $inputData = [];

        // Get all VNPay parameters
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == config('constants.VNP.PREFIX')) {
                $inputData[$key] = $value;
            }
        }

        // Get secure hash
        $vnp_SecureHash = $inputData[config('constants.VNP.FIELD.HASH')] ?? null;
        unset($inputData[config('constants.VNP.FIELD.HASH')]);

        // Sort input data
        ksort($inputData);

        // Create hash data string
        $hashData = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        // Calculate secure hash
        $secureHash = hash_hmac('sha512', $hashData, config('constants.ENV.VNP_HASH_SECRET'));

        // Get transaction details
        $vnpTranId = $inputData['vnp_TransactionNo'] ?? null;
        $vnp_BankCode = $inputData['vnp_BankCode'] ?? null;
        $vnp_Amount = ($inputData['vnp_Amount'] ?? 0) / 100;
        $orderId = $inputData['vnp_TxnRef'] ?? null;

        try {
            // Check secure hash
            if ($secureHash == $vnp_SecureHash) {
                // TODO: Get order from database
                // Example: $order = DB::table('orders')->where('id', $orderId)->first();
                $order = null; // Replace with database query

                // check order with database data to verify on top of signature hash

                // if ($order != null) {
                //     if ($order->amount == $vnp_Amount) {
                //         if ($order->status == 0) { // Assuming 0 is pending status
                // Check payment status
                if ($inputData['vnp_ResponseCode'] == '00' || $inputData['vnp_TransactionStatus'] == '00') {
                    $status = 1; // Payment successful
                } else {
                    $status = 2; // Payment failed
                }

                // TODO: Update order status in database
                // Example:
                // DB::table('orders')
                //     ->where('id', $orderId)
                //     ->update([
                //         'status' => $status,
                //         'transaction_id' => $vnpTranId,
                //         'bank_code' => $vnp_BankCode,
                //         'updated_at' => now()
                //     ]);

                $returnData = [
                    'RspCode' => '00',
                    'Message' => 'Confirm Success',
                ];
                //         } else {
                //             $returnData = [
                //                 'RspCode' => '02',
                //                 'Message' => 'Order already confirmed'
                //             ];
                //         }
                //     } else {
                //         $returnData = [
                //             'RspCode' => '04',
                //             'Message' => 'Invalid amount'
                //         ];
                //     }
                // } else {
                //     $returnData = [
                //         'RspCode' => '01',
                //         'Message' => 'Order not found'
                //         ];
                // }
            } else {
                $returnData = [
                    'RspCode' => '97',
                    'Message' => 'Invalid signature'
                ];
            }
        } catch (\Exception $e) {
            Log::error('VNPay IPN Error: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'transaction_id' => $vnpTranId,
                'input_data' => $inputData,
                'trace' => $e->getTraceAsString()
            ]);

            $returnData = [
                'RspCode' => '99',
                'Message' => 'Unknown error',
            ];
        }

        // Log the final response
        Log::info('VNPay IPN - Response', [
            'order_id' => $orderId,
            'transaction_id' => $vnpTranId,
            'response' => $returnData,
            'payment_status' => $inputData['vnp_ResponseCode'] ?? null,
            'transaction_status' => $inputData['vnp_TransactionStatus'] ?? null
        ]);

        return response()->json($returnData);
    }

    private function validateChecksum($data)
    {
        $inputData = [];
        // get fields with prefix 'vnp_'
        foreach($data as $key => $value){
            if(substr($key, 0, 4) == config('constants.VNP.PREFIX')){
                $inputData[$key] = $value;
            }
        }

        $checksum = $inputData[config('constants.VNP.FIELD.HASH')] ?? null;
        unset($inputData[config('constants.VNP.FIELD.HASH')]); // remove checksum field value
        ksort($inputData); // sort by key alphabet
        
        // format data string for checksum validation
        $dataString = '';
        foreach($inputData as $key => $value){
            $dataString .= $key .'='. $value .'&';
        }
        $dataString = substr($dataString,0,-1); // remove the last '&'

        $validation = hash_hmac('sha512', $dataString, config('constants.ENV.VNP_HASH_SECRET'));
        return $validation === $checksum;
    }
}
