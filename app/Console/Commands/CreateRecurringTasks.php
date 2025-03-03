<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\TaskController;

class CreateRecurringTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recurring-tasks:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate recurring tasks for today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Call the function from the TaskController
        app(TaskController::class)->checkAndCreateTasks();

        $this->info('Recurring tasks created successfully.');
    }
}
