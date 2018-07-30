<?php

namespace App;

use Auth;

use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
	protected $fillable = ['updated_at'];

	public function participants()
    {
    	return $this->hasMany('App\Participant');
    }

	public function first_participant()
    {
    	return $this->hasOne('App\Participant')->where('user_id', '!=', \Auth::id());
    }    

    public function messages()
    {
    	return $this->hasMany('App\Message');
    }

    // New message
    public function newMessage($body)
    {
    	self::update(['updated_at' => \Carbon\Carbon::now()]);
    	return self::messages()->create(['user_id' => Auth::id(), 'body' => $body]);
    }

    public function getReadAttribute()
    {
    	return $this->participants->where('user_id', \Auth::id())->first()->read_at;
    }

    // Add participant method
}
