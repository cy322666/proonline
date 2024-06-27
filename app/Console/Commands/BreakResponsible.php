<?php

namespace App\Console\Commands;

use App\Bitrix24\Bitrix24API;
use App\Models\Response;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BreakResponsible extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:break-response';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bx24 = new Bitrix24API('https://crm.pro-online.ru/rest/1/eu2xzb8lvgpu5o2m/');

        $responses = Response::query()
            ->where('status', 0)
            ->where('created_at', '<', Carbon::now()->subMinutes(15))
            ->get();

        foreach ($responses as $response) {

            if ($response->type == 'deal')

                $bx24->updateDeal($response->entity_id, [
                    'ASSIGNED_BY_ID' => $response->user_id_at,
                ]);
            else
                $bx24->updateCompany($response->entity_id, [
                    'ASSIGNED_BY_ID' => $response->user_id_at,
                ]);

            $response->status = 1;
            $response->save();
        }
    }
}
