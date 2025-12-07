<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_id',
        'title',
        'content',
        'is_completed',
        'attachment_path'];

    public function project() {
        return $this->belongsTo(Project::class);
    }


    protected static function booted()
    {
        static::created(function (Task $task) {
            event(new \App\Events\TaskCreated($task));
        });
    }
}
