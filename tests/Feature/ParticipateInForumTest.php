<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ParticipateInForumTest extends TestCase
{
    use DatabaseMigrations;

    public function test_unauthenticated_user_may_not_add_replies()
    {
        $this->expectException('Illuminate\Auth\AuthenticationException');

        $this->withoutExceptionHandling()
            ->post('/threads/some-channel/1/replies', []);
    }

    public function test_an_authenticated_user_may_participate_in_forum_threads()
    {
        /*$user = create('App\User');
        $this->be($user);*/

        $this->signIn();

        $thread = create('App\Thread');
        $reply = make('App\Reply');

        $this->withoutExceptionHandling()
            ->post($thread->path() . '/replies', $reply->toArray());

        $this->get($thread->path())
            ->assertSee($reply->body);
    }

    public function test_a_reply_requires_a_body()
    {

        $this->signIn();

        $thread = create('App\Thread');
        $reply = make('App\Reply',['body' => null]);

        $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertSessionHasErrors('body');

    }
}
