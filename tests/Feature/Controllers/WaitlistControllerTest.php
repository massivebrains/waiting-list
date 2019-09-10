<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Mail\SubscriberJoined;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use \Faker;

class WaitlistControllerTest extends TestCase
{
    /** @test */
    public function it_should_fail_when_no_email_is_provided()
    {
        $this->post('/subscribed', ['email' => ''])
        ->assertStatus(302)
        ->assertSessionHasErrors('email');
    }

    /** @test */
    public function it_should_fail_when_email_is_invalid()
    {
        $this->post('/subscribed', ['email' => 'invalid-payload'])
        ->assertStatus(302)
        ->assertSessionHasErrors('email');
    }

    /** @test */
    public function it_should_fail_when_email_exists()
    {
        Subscriber::create(['email' => 'vadeshayo@gmail.com']);

        $this->post('/subscribed', ['email' => 'vadeshayo@gmail.com'])
        ->assertStatus(302)
        ->assertSessionHasErrors('email');
    }

    /** @test */
    public function it_should_subscribe_user_and_send_email()
    {
        

        $email = $email = (Faker\Factory::create())->safeEmail;

        $this->post('/subscribed', ['email' => $email])
        ->assertRedirect('/subscribed');
        
        $this->assertDatabaseHas('subscribers', [

            'email' => $email
        ]);

        Mail::fake();
        Mail::assertNothingSent();

        $subscriber = Subscriber::whereEmail($email)->first();

        Mail::assertSent(SubscriberJoined::class, function ($mail) use ($subscriber){

            return $mail->hasTo($subscriber->email);
        });

    }

}
