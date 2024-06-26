<?php

namespace App\Http\Controllers;

use App\Bitrix24\Bitrix24API;
use App\Bitrix24\Company;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ResponseController extends Controller
{
    public function company(Request $request)
    {
        Log::info(__METHOD__, $request->toArray());

        $userId = explode('_', $request->user)[1];
        $companyId = explode('_', $request->company)[1];

        if ($userId && $companyId)

            Artisan::call('app:change-response', [
                'user_id' => $userId,
                'company_id' => $companyId,
            ]);
    }

    public function cron()
    {
        Artisan::call('app:break-response');
    }
}
