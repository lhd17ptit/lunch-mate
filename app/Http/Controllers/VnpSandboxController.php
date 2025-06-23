<?php

namespace App\Http\Controllers;

use App\Http\Requests\VNP\ProcessPaymentRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Repositories\TransactionRepository;
use Exception;
use App\Services\VnpayService;

class VnpSandboxController extends Controller
{
	protected $transactionRepo;
	protected $vnpayService;

	public function __construct(
		TransactionRepository $transactionRepo,
		VnpayService $vnpayService
	) {
		$this->transactionRepo = $transactionRepo;
		$this->vnpayService = $vnpayService;
	}

	public function index(Request $request)
	{
		return view('vnpay.sandbox');
	}

	public function process(ProcessPaymentRequest $request)
	{
		$vnp_Url = $this->vnpayService->processPayment($request);
		if ($request->ajax()) {
			return response()->json([
				'redirect_url' => $vnp_Url
			]);
		}
		return redirect()->away($vnp_Url);
	}

	public function return(Request $request)
	{
		$data = $this->vnpayService->getReturnData($request);
		return view('vnpay.return', $data);
	}

	public function ipn(Request $request)
	{
		$returnData = $this->vnpayService->handleIpn($request);
		return response()->json($returnData);
	}

}
