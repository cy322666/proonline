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

        Artisan::call('app:change-response', [
            'user_id' => $request->user,
            'company_id' => explode('_', $request->document_id[2])[1],
        ]);
    }

    public function cron()
    {
        Artisan::call('app:break-response');
    }
}