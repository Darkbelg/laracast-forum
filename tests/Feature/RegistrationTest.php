<?php

namespace Tests\Feature;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_a_confirmation_email_is_sent_upon_registration()
    {
        //The mail sent for verifications of account or activation of account is not a mail but a notification.
        //So we need to fake the notifications to verify mail.
        Notification::fake();

        $user = create('App\User',['email_verified_at' => null]);

        $result = event(new Registered($user));

        Notification::assertSentTo($user, VerifyEmail::class);

        //There is a alternative way to test. This will just test if the event is dispatched.
        //see test_a_confirmation_event_is_activated_upon_registration
    }

    public function test_a_confirmation_event_is_activated_upon_registration()
    {
        Event::fake();
    
        $this->post(route('register'), [
            'name' => 'Joe',
            'email' => 'testemail@test.com',
            'password' => 'passwordtest',
            'password_confirmation' => 'passwordtest'
        ]);
    
        Event::assertDispatched(Registered::class);
    }
}
