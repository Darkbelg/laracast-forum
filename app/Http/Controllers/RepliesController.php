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
        $reply = $thread->addReply([
                'body' => request('body'),
                'user_id' => auth()->id()
            ]);

        \preg_match_all('/\@([^\s\.]+)/',$reply->body,$matches);

        foreach ($matches[1] as $name) {
            $user = User::whereName($name)->first();

            if($user){
                $user->notify(new YouWereMentioned($reply));
            }
        }

        $reply->load('owner');
    }

    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);

        try {
            request()->validate([
                'body' => ['required', new SpamFree]
            ]);

            $reply->update(['body' => request('body')]);
        } catch (\Exception $e) {
            return response(
                'Sorry, your reply could not be saved ath this time.',
                422
            );
        }
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
