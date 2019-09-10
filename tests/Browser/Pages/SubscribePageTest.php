<?php

namespace Tests\Browser\Pages;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use \Faker;

class SubscribePageTest extends DuskTestCase
{
    /** @test */
    public function it_should_render_subscribe_page_successfully()
    {
        $this->browse(function (Browser $browser) {

            $browser->visit('/subscribe')
            ->assertSee('marketplace')
            ->assertSee('Licensed');
        });
    }

    /** @test */
    public function it_should_fail_on_email_validation()
    {
        $emails = ['', 'invalid*com.com'];

        foreach($emails as $email){

            $this->browse(function (Browser $browser) use($email) {

                $browser->visit('/subscribe')
                ->waitForText('Email Address')
                ->type('email', $email)
                ->press('button[type=submit]')
                ->assertFocused('#email')
                ->assertPathIs('/subscribe');
            });

        }
    }

    /** @test */
    public function it_should_subscribe_to_waiting_list()
    {
        $email = (Faker\Factory::create())->safeEmail;

        // It should subscribe successfully.
        $this->browse(function (Browser $browser) use($email) {

            $browser->visit('/subscribe')
            ->waitForText('Email Address')
            ->type('email', $email)
            ->press('button[type=submit]')
            ->assertPathIs('/subscribed');
        });

        //It should return with error: unique email validation.
        $this->browse(function (Browser $browser) use ($email) {

            $browser->visit('/subscribe')
            ->waitForText('Email Address')
            ->type('email', $email)
            ->press('button[type=submit]')
            ->assertPathIs('/subscribe')
            ->assertSee('The email has already been taken');
        });
    }
}
