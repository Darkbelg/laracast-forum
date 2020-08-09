<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class MentionUsersTest extends TestCase
{
    use DatabaseMigrations;

    public function test_mention_users_in_a_reply_are_notified()
    {
        $john = create('App\User', ['name' => 'JohnDoe']);

        $this->signIn($john);

        $jane = create('App\User', ['name' => 'JaneDoe']);

        $thread = create('App\Thread');

         $reply = make('App\Reply',[
             'body' => '@JaneDoe look at this.'
         ]);

            $this->withoutExceptionHandling();

         $this->json('post',$thread->path() . '/replies', $reply->toArray());

         $this->assertCount(1, $jane->notifications);
    }
}
