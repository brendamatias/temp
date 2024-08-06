<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\Repositories\InvoiceRepository;
use App\Domain\Repositories\PreferencesRepository;
use App\Infrastructure\Services\NotificationService;

class CheckMeiLimit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mei:check-limit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica se o limite anual do MEI foi atingido e envia notificações';

    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly PreferencesRepository $preferencesRepository
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $users = \App\Infrastructure\Database\Models\User::all();

        foreach ($users as $user) {
            $preferences = $this->preferencesRepository->findByUserId($user->id);
            if (!$preferences) {
                continue;
            }

            $currentYear = date('Y');
            $currentAmount = $this->invoiceRepository->getTotalRevenueByYear($currentYear);
            $limit = $preferences->getMeiAnnualLimit();

            $notificationService = new NotificationService($preferences);
            $notificationService->sendLimitAlert($currentAmount, $limit);
        }

        $this->info('Verificação de limite do MEI concluída.');
    }
}
