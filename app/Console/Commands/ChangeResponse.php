<?php

namespace App\Console\Commands;

use App\Bitrix24\Bitrix24API;
use App\Models\Response;
use Illuminate\Console\Command;

class ChangeResponse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:change-response {user_id} {company_id}';

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
        $userId = $this->argument('user_id');
        $companyId = $this->argument('company_id');

        $bx24 = new Bitrix24API('https://crm.pro-online.ru/rest/1/eu2xzb8lvgpu5o2m/');

        //первое нажатие, запоминаем и перезаписываем ответственного
        if (!Response::query()
            ->where('entity_id', $companyId)
            ->where('type', 'company')
            ->where('status', 0)
            ->exists()) {

            //получаем все сделки
            $deals = $bx24->getDealList([
                'COMPANY_ID' => $companyId
            ]);

            foreach ($deals->current() as $deal) {

                $bx24->updateDeal($deal['ID'], [
                    'ASSIGNED_BY_ID' => $userId
                ]);

                Response::query()->create([
                    'type' => 'deal',
                    'entity_id'  => $deal['ID'],
                    'user_id_at' => $deal['ASSIGNED_BY_ID'],
                    'user_id_to' => $userId,
                ]);
            }

            $company = $bx24->getCompany($companyId);

            Response::query()->create([
                'type' => 'company',
                'entity_id'  => $company['ID'],
                'user_id_at' => $company['ASSIGNED_BY_ID'],
                'user_id_to' => $userId,
            ]);

            $bx24->updateCompany($companyId, [
                'ASSIGNED_BY_ID' => $userId
            ]);

        } else {

            $deals = $bx24->getDealList([
                'COMPANY_ID' => $companyId
            ]);

            foreach ($deals->current() as $deal) {

                $responseDeal = Response::query()
                    ->where('entity_id', $deal['ID'])
                    ->where('type', 'deal')
                    ->where('status', 0)
                    ->first();

                if (!$responseDeal) continue;

                $bx24->updateDeal($responseDeal->entity_id, [
                    'ASSIGNED_BY_ID' => $responseDeal->user_id_at,
                ]);

                $responseDeal->status = 1;
                $responseDeal->save();
            }

            $responseCompany = Response::query()
                ->where('entity_id', $companyId)
                ->where('type', 'company')
                ->where('status', 0)
                ->first();

            $bx24->updateCompany($responseCompany->entity_id, [
                'ASSIGNED_BY_ID' => $responseCompany->user_id_at,
            ]);

            $responseCompany->status = 1;
            $responseCompany->save();
        }
    }
}
