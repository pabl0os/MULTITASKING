<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'description', 'global_priority', 'global_deadline', 'max_in_process_per_user', 'status'])]
class Project extends Model
{
    protected function casts(): array
    {
        return [
            'global_deadline' => 'datetime',
            'global_priority' => 'integer',
            'max_in_process_per_user' => 'integer',
        ];
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function histories()
    {
        return $this->hasMany(ProjectHistory::class);
    }
}
