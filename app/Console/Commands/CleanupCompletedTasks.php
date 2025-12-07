<?php

namespace App\Console\Commands;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanupCompletedTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-completed-tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Supprime les taches completées vieilles de plus d un mois';

    /**
     * Execute the console command.
     */
    public function handle()
    {
       $deleteTask = Task::where('is_completed', true)
            ->where('created_at', '<', Carbon::now()->subMonth())
            ->delete();

       $this->info("$deleteTask tache supprimé");
    }
}
