<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ParticipateInForumTest extends TestCase
{
    use RefreshDatabase;

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


        //The following lines fail because we use vue components now
        // $this->get($thread->path())
        //     ->assertSee($reply->body);
        $this->assertDatabaseHas('replies', ['body' => $reply->body]);
        $this->assertEquals(1, $thread->fresh()->replies_count);
    }

    public function test_a_reply_requires_a_body()
    {

        $this->signIn();

        $thread = create('App\Thread');
        $reply = make('App\Reply', ['body' => null]);

        $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertSessionHasErrors('body');
        }

    public function test_unauthorized_users_cannot_delete_reply()
    {
        $reply = create('App\Reply');

        $this->delete("/replies/{$reply->id}")
            ->assertRedirect('login');

        // $this->withoutExceptionHandling();
        $this->signIn()
            ->delete("/replies/{$reply->id}")
            ->assertStatus(403);
    }

    public function test_unauthorized_users_cannot_update_reply()
    {
        $reply = create('App\Reply');

        $this->patch("/replies/{$reply->id}")
            ->assertRedirect('login');

        // $this->withoutExceptionHandling();
        $this->signIn()
            ->patch("/replies/{$reply->id}")
            ->assertStatus(403);
    }

    public function test_authorized_users_can_delete_replies()
    {
        $this->signIn();
        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $this->delete("/replies/{$reply->id}")->assertStatus(302);

        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
        $this->assertEquals(0, $reply->thread->fresh()->replies_count);
    }


    public function test_authorized_users_can_update_replies()
    {
        $this->signIn();

        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $updatedReply = 'You been changed, fool.';

        $this->withoutExceptionHandling();
        $this->patch("/replies/{$reply->id}", ['body' => $updatedReply]);

        $this->assertDatabaseHas('replies', ['id' => $reply->id, 'body' => $updatedReply]);
    }

    public function test_replies_that_contain_spam_may_not_be_created()
    {
        $this->signIn();

        $thread = create('App\Thread');
        $reply = make('App\Reply', ['body' => 'Yahoo customer support']);

        $this->json('post',$thread->path() . '/replies', $reply->toArray())
            ->assertStatus(422);
    }

    public function test_users_may_only_reply_a_maximum_of_once_per_minute()
    {
        $this->signIn();

        $thread = create('App\Thread');
        $reply = make('App\Reply');

        $this->post($thread->path() . '/replies', $reply->toArray())->assertStatus(201);
        $this->post($thread->path() . '/replies', $reply->toArray())->assertStatus(429);
    }
}
