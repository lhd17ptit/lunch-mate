<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\MicrosoftAuthService;
use App\Models\OAuthToken;
use Illuminate\Support\Facades\Log;

class MicrosoftAuthController extends Controller
{
    protected MicrosoftAuthService $service;

    public function __construct(MicrosoftAuthService $service)
    {
        $this->service = $service;
    }

    public function redirect()
    {
        $query = http_build_query([
            'client_id'     => config('services.microsoft.client_id'),
            'response_type' => 'code',
            'redirect_uri'  => config('services.microsoft.redirect'),
            'response_mode' => 'query',
            'scope'         => 'offline_access Chat.ReadWrite',
            'state'         => csrf_token(),
        ]);

		$tenant = config('services.microsoft.tenant_id');

		return "https://login.microsoftonline.com/$tenant/oauth2/v2.0/authorize?$query";
        return redirect("https://login.microsoftonline.com/$tenant/oauth2/v2.0/authorize?$query");
    }

    public function callback(Request $request)
    {
		Log::info("REQUEST");
		Log::info($request->all());
        // $code = $request->get('code');
        // if (!$code) {
        //     return response()->json(['error' => 'Authorization code missing'], 400);
        // }

        // $token = $this->service->exchangeCode($code, $request->user()->id);

        // return response()->json($token);
		return false;
    }
}
