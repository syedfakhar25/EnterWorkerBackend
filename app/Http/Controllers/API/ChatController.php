<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Chat;
use App\Models\User;
use App\Events\ChatEvent;

class ChatController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function index()
    {
    	return view('chat.chat');
    }

    public function fetchAllMessages(Request $request)
    {
    	$request->validate([
            'chatwith_id' => 'required|numeric',
            'user_id' => 'required|numeric'
	        ]);

    	return Chat::where('user_id',$request->chatwith_id)->where('chatwith_id',$request->user_id)->orWhere('chatwith_id',$request->chatwith_id)->where('user_id',$request->user_id)->with('user')->get();
    }

    public function sendMessage(Request $request)
    {
    	$request->validate([
            'chatwith_id' => 'required|numeric',
            'message' => 'required|string'
	        ]);

    	$chat = auth()->user()->messages()->create([ 
            'message' => $request->message,
            'chatwith_id' => $request->chatwith_id
        ]);
    	broadcast(new ChatEvent($chat->load('user')))->toOthers();

    	return ['status' => 'success'];
    }

    public function usersList(Request $request)
    {
    	$users= User::get();
    	foreach ($users as $key => $value) {
    		$value->live_status=0;
    	}
    	return $users;
    }
}
