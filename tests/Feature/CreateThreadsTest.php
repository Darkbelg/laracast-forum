<?php

namespace Tests\Feature;

use App\Activity;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Notifications\Action;
use Tests\TestCase;

class CreateThreadsTest extends TestCase
{

    use DatabaseMigrations;

    public function test_guests_may_not_create_threads()
    {
        $this->get('/threads/create')
            ->assertRedirect('/login');

        $this->post('/threads')
            ->assertRedirect('/login');
    }

    public function test_new_user_must_first_confirm_their_email_address_before_creating_threads()
    {
        $this->publishThread([],create('App\User',['email_verified_at' => null]))
        ->assertRedirect('/email/verify');
    }

    public function test_an_authenticated_user_can_create_new_forum_threads()
    {
        //$this->actingAs(create('App\User'));
        $this->signIn();

        $thread = make('App\Thread');

        $response = $this->withoutExceptionHandling()
            ->post('/threads', $thread->toArray());

        $this->get($response->headers->get('Location'))
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }

    public function test_a_thread_requires_a_title()
    {
        $this->publishThread(['title' => null])
        ->assertSessionHasErrors('title');
    }

    public function test_a_thread_requires_a_body()
    {
        $this->publishThread(['body' => null])
        ->assertSessionHasErrors('body');
    }

    public function test_a_thread_requires_a_channel()
    {

        factory('App\Channel',2)->create();

        $this->publishThread(['channel_id' => null])
        ->assertSessionHasErrors('channel_id');

        $this->publishThread(['channel_id' => 9999])
        ->assertSessionHasErrors('channel_id');
    }

    public function test_unauthorized_users_may_not_delete_threads()
    {
        $thread = create('App\Thread');

        $this->delete($thread->path())->assertRedirect('/login');

        $this->signIn();
        $this->delete($thread->path())->assertStatus(403);
    }

    public function test_authorized_user_can_delete_threads()
    {
        $this->withoutExceptionHandling();
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $reply = create('App\Reply', ['thread_id' => $thread->id]);

        $response = $this->json('DELETE', $thread->path());

        $response->assertStatus(204);

        $this->assertDatabaseMissing('threads',['id' => $thread->id]);
        $this->assertDatabaseMissing('replies',['id' => $reply->id]);


        // $this->assertDatabaseMissing('activities',[
        //     'subject_id' => $thread->id,
        //     'subject_type' =>get_class($thread)
        //     ]);
        // $this->assertDatabaseMissing('activities',[
        //     'subject_id' => $reply->id,
        //     'subject_type' =>get_class($reply)
        //     ]);
        //Following line has the same wanted function as the two statemens above.
        $this->assertEquals(0,Activity::count());
    }
    
    public function publishThread($overrides,$user = null)
    {
        $this->signIn($user);

        $thread = make('App\Thread',$overrides);

        return $this->post('/threads',$thread->toArray());
    }
}
