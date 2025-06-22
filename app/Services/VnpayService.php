<?php

namespace App\Services;

use App\Http\Requests\VNP\ProcessPaymentRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use App\Repositories\TransactionRepository;
use Exception;

class VnpayService
{
    protected $transactionRepo;

    public function __construct(TransactionRepository $transactionRepo)
    {
        $this->transactionRepo = $transactionRepo;
    }

    public function processPayment(ProcessPaymentRequest $request)
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $transactionReference = time();
        $inputData = [
            "vnp_Amount" => ($request->vnp_Amount ?? 0) * 100,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => now()->format('YmdHis'),
            "vnp_ExpireDate" => now()->addMinutes(5)->format('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $request->ip() ?? $request->ip_address,
            "vnp_Locale" => config('app.locale', 'en') == 'vi' ? 'vn' : 'en',
            "vnp_OrderInfo" => $request->vnp_OrderInfo ?? null,
            "vnp_OrderType" => '100000',
            "vnp_ReturnUrl" => URL::route('vnpay.return', [], true),
            "vnp_TmnCode" => config('constants.ENV.VNP_TMN_CODE'),
            "vnp_TxnRef" => $transactionReference,
            "vnp_Version" => "2.1.0"
        ];
        $userId = auth()->id() ?? null;
        $transactionData = [
            'order_id' => $transactionReference,
            'user_id' => $userId,
            'note' => $request->vnp_OrderInfo ?? null,
            'amount' => $request->vnp_Amount ?? 0,
            'guest_uid' => $userId ? null : bin2hex(random_bytes(16)),
            'raw_payload' => json_encode($inputData),
        ];
        try {
            $this->transactionRepo->create($transactionData);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
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
        $vnp_Url = config('constants.ENV.VNP_URL') . "?" . $query . "&" . config('constants.VNP.FIELD.HASH') . "=" . $vnpSecureHash;
        return $vnp_Url;
    }

    public function getReturnData(Request $request)
    {
        $data = [
            'status' => $request[config('constants.VNP.FIELD.RESPONSE_CODE')] ?? null,
            'transaction_id' => $request[config('constants.VNP.FIELD.TRANSACTION_ID')] ?? null,
            'message' => $request[config('constants.VNP.FIELD.MESSAGE')] ?? null,
        ];
        $validateChecksum = $this->validateChecksum($request->all());
        if (!$validateChecksum) {
            $data['status'] = config('constants.VNP.RESPONSE_CODE.INVALID_SIGNATURE');
        }
        return $data;
    }

    public function handleIpn(Request $request)
    {
        Log::info('VNPay IPN - Raw Request', [
            'all_data' => $request->all(),
            'headers' => $request->headers->all(),
            'ip' => $request->ip(),
            'method' => $request->method(),
            'url' => $request->fullUrl()
        ]);
        $inputData = [];
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == config('constants.VNP.PREFIX')) {
                $inputData[$key] = $value;
            }
        }
        $vnp_SecureHash = $inputData[config('constants.VNP.FIELD.HASH')] ?? null;
        unset($inputData[config('constants.VNP.FIELD.HASH')]);
        ksort($inputData);
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
        $secureHash = hash_hmac('sha512', $hashData, config('constants.ENV.VNP_HASH_SECRET'));
        $vnpTranId = $inputData['vnp_TransactionNo'] ?? null;
        $vnp_Amount = ($inputData['vnp_Amount'] ?? 0) / 100;
        $transactionReference = $inputData['vnp_TxnRef'] ?? null;
        try {
            if ($secureHash == $vnp_SecureHash) {
                $order = $this->transactionRepo->query()->where('order_id', $transactionReference)->first();
                if ($order != null) {
                    if ($order->amount == $vnp_Amount) {
                        if (empty($order->status_code)) {
                            if ($inputData['vnp_ResponseCode'] == '00' || $inputData['vnp_TransactionStatus'] == '00') {
                                $status = Transaction::STATUS_CODE['SUCCESS'];
                            } else {
                                $status = Transaction::STATUS_CODE['FAILED'];
                            }
                            $order->status_code = $status;
                            $order->save();
                            $returnData = [
                                'RspCode' => '00',
                                'Message' => 'Confirm Success',
                            ];
                        } else {
                            $returnData = [
                                'RspCode' => '02',
                                'Message' => 'Order already confirmed'
                            ];
                        }
                    } else {
                        $returnData = [
                            'RspCode' => '04',
                            'Message' => 'Invalid amount'
                        ];
                    }
                } else {
                    $returnData = [
                        'RspCode' => '01',
                        'Message' => 'Order not found'
                    ];
                }
            } else {
                $returnData = [
                    'RspCode' => '97',
                    'Message' => 'Invalid signature'
                ];
            }
        } catch (Exception $e) {
            Log::error('VNPay IPN Error: ' . $e->getMessage(), [
                'order_id' => $transactionReference,
                'transaction_id' => $vnpTranId,
                'input_data' => $inputData,
                'trace' => $e->getTraceAsString()
            ]);
            $returnData = [
                'RspCode' => '99',
                'Message' => 'Unknown error',
            ];
        }
        Log::info('VNPay IPN - Response', [
            'order_id' => $transactionReference,
            'transaction_id' => $vnpTranId,
            'response' => $returnData,
            'payment_status' => $inputData['vnp_ResponseCode'] ?? null,
            'transaction_status' => $inputData['vnp_TransactionStatus'] ?? null
        ]);
        return $returnData;
    }

    public function validateChecksum($data)
    {
        $inputData = [];
        foreach ($data as $key => $value) {
            if (substr($key, 0, 4) == config('constants.VNP.PREFIX')) {
                $inputData[$key] = $value;
            }
        }
        $checksum = $inputData[config('constants.VNP.FIELD.HASH')] ?? null;
        unset($inputData[config('constants.VNP.FIELD.HASH')]);
        ksort($inputData);
        $dataString = '';
        foreach ($inputData as $key => $value) {
            $dataString .= $key . '=' . $value . '&';
        }
        $dataString = substr($dataString, 0, -1);
        $validation = hash_hmac('sha512', $dataString, config('constants.ENV.VNP_HASH_SECRET'));
        return $validation === $checksum;
    }
} 