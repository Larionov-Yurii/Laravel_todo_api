<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubTask extends Model
{
    use HasFactory;

    protected $table = 'subtasks';

    protected $fillable = [
        'parent_task_id',
        'status',
        'priority',
        'title',
        'description',
    ];

    public function parentTask()
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function subtasks()
    {
        return $this->hasMany(SubTask::class, 'parent_task_id');
    }
}
