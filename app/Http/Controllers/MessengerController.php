<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

class MessengerController extends Controller
{
	private $user;

	public function __construct()
	{
		$this->user = Auth::user();
	}
    
    public function threads()
    {
    	return $this->user->threads;
    }

    public function thread($id)
    {
    	return $this->user->threads->where('id', $id)->first()->append('read');
    }

	public function messages($id)
    {
    	return $this->thread($id)->messages;
    }

	public function addMessage($id)
    {
    	return $this->thread($id)->newMessage(request()->body);
    }

	public function newThreadWith($user_id)
	{
		$thread = new \App\Thread;
    	$thread->save();

    	// add the participants
    	$thread->participants()->createMany([
    		['user_id' => $user_id], // $user_id is the new participant id
    		['user_id' => $this->user->id]
    	]);

    	return $thread;
	}    

    public function newMessage()
    {
    	// Needs special validation!!! If thread with that user already exist for example!
    	// For example: find thread with first_participant where first_participant is request()->user_id

    	// create a thread with new participant
    	$thread = $this->newThreadWith(request()->user_id);

    	// post a message to created thread
    	$this->thread($thread->id)->newMessage(request()->body);
    	return $thread->load('first_participant.user');
    }

	public function search()
    {
    	return \App\User::where('id', '!=', $this->user->id)
    						->where('name', 'like', '%'.request()->text.'%')
    						->limit(10)
    						->get();
    }

    public function seen() {
    	$thread = \App\Thread::find(request()->thread_id);
    	$participant = $thread->participants()->where('user_id', $this->user->id)->first();
    	$participant->update(['read_message_id' => request()->read_message_id, 'read_at' => \Carbon\Carbon::now()]);
    	return 'seen';
    }
}
