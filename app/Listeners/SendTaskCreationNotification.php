<?php

namespace App\Listeners;

use App\Events\TaskCreated;
use App\Models\Task;
use App\Notifications\NewTaskAssigned;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTaskCreationNotification implements ShouldQueue
{
    use InteractsWithQueue;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(TaskCreated $event): void
    {
        $task = $event->task;

        $task->loadMissing('project.user');

        $owner = $task->project?->user;

        if ($owner) {
            $owner->notify(new NewTaskAssigned($task));
        }
    }
}

