<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';

    protected $fillable = [
        'user_id',
        'status',
        'priority',
        'title',
        'description',
    ];

    public function subtasks()
    {
        return $this->hasMany(SubTask::class, 'parent_task_id');
    }
}
