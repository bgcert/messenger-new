<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{

	protected $fillable = ['user_id', 'read_message_id', 'read_at'];

    public function thread()
    {
    	return $this->belongsTo('App\Thread');
    }

    public function user()
    {
    	return $this->belongsTo('App\User');
    }
}
