<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Notifications\DatabaseNotification;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling()
        ->signIn();
    }

    public function test_a_notification_is_prepared_when_a_subscribed_thread_receives_a_new_reply_that_is_not_by_the_current_user()
    {
        $thread = create('App\Thread')->subscribe();

        $this->assertCount(0, auth()->user()->notifications);

        $thread->addReply([
            'user_id' => auth()->id(),
            'body' => 'Some Reply Here. You stink al bundy.'
        ]);

        $this->assertCount(0, auth()->user()->fresh()->notifications);

        $thread->addReply([
            'user_id' => create('App\User')->id,
            'body' => 'Some Reply Here. Hey you'
        ]);

        $this->assertCount(1, auth()->user()->fresh()->notifications);
    }

    public function test_a_user_can_fetch_their_unread_notifications()
    {
        create(DatabaseNotification::class);
        /* Above is the same as below but as a factory methode.
         $thread = create('App\Thread')->subscribe();
         $thread->addReply([
             'user_id' => create('App\User')->id,
             'body' => 'Some Reply Here. Hey you'
         ]);
        */

        $this->assertCount(
            1,
            $this->getJson("/profiles/" . auth()->user()->name . "/notifications")->json()
        );
    }

    public function test_a_user_can_mark_a_notification_as_read()
    {
        create(DatabaseNotification::class);

        tap(auth()->user(), function($user){
            $this->assertCount(1, $user->unreadNotifications);
            
            $this->delete("/profiles/{$user->name}/notifications/" . $user->unreadNotifications->first()->id);
    
            $this->assertCount(0, $user->fresh()->unreadNotifications);
        });
    }
}
