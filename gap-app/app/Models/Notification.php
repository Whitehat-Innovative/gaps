<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    
    public function users()
    {
        return $this->hasMany(NotifyUser::class, 'notification_id', 'id');
    }
}
