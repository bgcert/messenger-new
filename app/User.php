<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getThreadsAttribute() {
    	$user_id = \Auth::id();
    	return \App\Thread::with('first_participant.user')
    						->whereHas('participants', function ($q) use ($user_id) {
					    		$q->where('user_id', $user_id);
					    	})->orderBy('updated_at', 'desc')->get();
    }
}
