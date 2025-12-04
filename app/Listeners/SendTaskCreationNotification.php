<?php

namespace App\Listeners;

use App\Events\TaskCreated;
use App\Models\Task;
use App\Notifications\NewTaskAssigned;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTaskCreationNotification
{
    /**
     * Create the event listener.
     */
    public function __construct(Task $task)
    {
        $this->task = $task;

    }

    /**
     * Handle the event.
     */
    public function handle(TaskCreated $event): void
    {
        $task = $event->task;

        $owner = $task->project()->user; //recup propiétaire du projet

        if ($owner) {
            $owner->notify(new NewTaskAssigned($task));
        } else{
            error('tache non notifiée');
        }
    }
}

