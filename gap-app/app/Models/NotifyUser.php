<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotifyUser extends Model
{
    
    protected $table = 'notify_users'; 
    protected $fillable = ['user_id', 'notification_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function notification()
    {
        return $this->belongsTo(Notification::class, 'notification_id', 'id');
    }
}
