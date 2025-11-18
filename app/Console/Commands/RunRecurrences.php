<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Recurrence;
use App\Models\Transaction;
use Carbon\Carbon;

class RunRecurrences extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * Exemplo de execução no terminal:
     * php artisan recurrences:run
     */
    protected $signature = 'recurrences:run';

    /**
     * Descrição do comando.
     */
    protected $description = 'Executa as recorrências ativas e cria novas transações conforme necessário';

    /**
     * Executa o comando.
     */
    public function handle()
    {
        $recurrences = Recurrence::where('active', true)
            ->where('next_date', '<=', now()->toDateString())
            ->get();

        if ($recurrences->isEmpty()) {
            $this->info('Nenhuma recorrência para processar.');
            return;
        }

        foreach ($recurrences as $r) {
            Transaction::create([
                'user_id' => $r->user_id,
                'category_id' => $r->category_id,
                'amount' => $r->amount,
                'direction' => $r->direction,
                'happened_at' => $r->next_date,
                'is_recurring' => true,
                'recurrence_id' => $r->id,
            ]);

            // Calcula próxima data
            $r->next_date = $this->calcNextDate($r->next_date, $r->frequency, $r->interval);
            $r->save();

            $this->info("Recorrência ID {$r->id} processada com sucesso. Próxima data: {$r->next_date}");
        }

        $this->info('Todas as recorrências foram processadas.');
    }

    /**
     * Calcula a próxima data de recorrência.
     */
    protected function calcNextDate($date, $frequency, $interval)
    {
        $d = Carbon::parse($date);

        switch ($frequency) {
            case 'daily':
                return $d->addDays($interval)->toDateString();
            case 'weekly':
                return $d->addWeeks($interval)->toDateString();
            case 'monthly':
                return $d->addMonths($interval)->toDateString();
            case 'yearly':
                return $d->addYears($interval)->toDateString();
            default:
                return $date; // caso frequência inválida
        }
    }
}
