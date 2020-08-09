<?php

namespace App\Listeners;

use App\Events\ThreadHasNewReply;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\YouWereMentioned;
use App\User;


class NotifyMentionedUsers
{
    /**
     * Handle the event.
     *
     * @param  ThreadHasNewReply  $event
     * @return void
     */
    public function handle(ThreadHasNewReply $event)
    {
        // collect($event->reply->mentionedUsers())
        // ->map(fn($name) => [
        //     User::where('name',$name)->first()
        // ])
        // ->filter()
        // ->each(fn($user)=>[
        //     $user->notify(new YouWereMentioned($event->reply))
        // ]);

        collect($event->reply->mentionedUsers())
        ->map(function ($name){
            return User::where('name',$name)->first();
        })
        ->filter()
        ->each(fn($user)=>[
            $user->notify(new YouWereMentioned($event->reply))
        ]);
    }
}
