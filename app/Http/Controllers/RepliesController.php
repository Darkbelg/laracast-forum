<?php

namespace App\Http\Controllers;

use App\Reply;
use App\Notifications\YouWereMentioned;
use App\Rules\SpamFree;
use App\Thread;
use App\User;
use App\Http\Requests\CreatePostRequest;
use Illuminate\Http\Request;

class RepliesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'index']);
    }

    public function index($channelId, Thread $thread)
    {
        return $thread->replies()->paginate(20);
    }

    public function store($channelId, Thread $thread, CreatePostRequest $form)
    {
        if ($thread->locked) {
            return response('Thread is locked', 422);
        }
        
        return $thread->addReply([
                'body' => request('body'),
                'user_id' => auth()->id()
            ])->load('owner');
    }

    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);

        request()->validate([
                'body' => ['required', new SpamFree]
            ]);

        $reply->update(['body' => request('body')]);
    }

    public function destroy(Reply $reply)
    {
        /*
        if($reply->user_id != auth()->id()){
             return response([],403);
         }
         This can be replaced by:
        */
        $this->authorize('update', $reply);

        $reply->delete();

        if (request()->expectsJson()) {
            return response(['status' => 'Reply deleted']);
        }

        return back();
    }
}
