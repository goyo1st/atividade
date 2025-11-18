<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\RunRecurrences;

class Kernel extends ConsoleKernel
{
    /**
     * Os comandos Artisan disponíveis na aplicação.
     *
     * @var array
     */
    protected $commands = [
        RunRecurrences::class,
    ];

    /**
     * Define os agendamentos de comandos da aplicação.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Executa o comando RunRecurrences todo dia à meia-noite
        $schedule->command('recurrences:run')->daily();

        // Exemplo de outros comandos agendados (opcional)
        // $schedule->command('emails:send')->hourly();
    }

    /**
     * Registra os comandos da aplicação.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
