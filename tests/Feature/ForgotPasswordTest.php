<?php

namespace DavidSchneider\LaravelCrewmate\Tests\Feature;

use DavidSchneider\LaravelCrewmate\Crewmate;
use DavidSchneider\LaravelCrewmate\Notifications\PasswordResetRequestNotification;
use DavidSchneider\LaravelCrewmate\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    protected function passwordRequestRoute()
    {
        return route('crewmate.password.request');
    }

    protected function passwordResetGetRoute($token)
    {
        return route('crewmate.password.reset', $token);
    }

    protected function passwordEmailGetRoute()
    {
        return route('crewmate.password.email');
    }

    protected function passwordEmailPostRoute()
    {
        return route('crewmate.password.email');
    }

    public function testUserCanViewAnEmailPasswordForm()
    {
        $response = $this->get($this->passwordRequestRoute());

        $response->assertSuccessful();
        $response->assertViewIs('crewmate::auth.passwords.email');
    }

    public function testUserCanViewAnEmailPasswordFormWhenAuthenticated()
    {
        $user = Crewmate::factory()->make();

        $response = $this->be($user, 'admin')->get($this->passwordRequestRoute());

        $response->assertSuccessful();
        $response->assertViewIs('crewmate::auth.passwords.email');
    }

    public function testUserReceivesAnEmailWithAPasswordResetLink()
    {
        Notification::fake();
        $user = Crewmate::factory()->create([
            'email' => 'john@example.com',
        ]);

        $response = $this->post($this->passwordEmailPostRoute(), [
            'email' => 'john@example.com',
        ]);
        $this->assertNotNull($token = DB::table('crewmate_password_resets')->first());
        Notification::assertSentTo($user, PasswordResetRequestNotification::class, function ($notification) {
            $response = $this->get($this->passwordResetGetRoute($notification->token));
            $response->assertStatus(200);
            return true;
        });
    }

    public function testUserDoesNotReceiveEmailWhenNotRegistered()
    {
        Notification::fake();

        $response = $this->from($this->passwordEmailGetRoute())->post($this->passwordEmailPostRoute(), [
            'email' => 'nobody@example.com',
        ]);

        $response->assertRedirect($this->passwordEmailGetRoute());
        $response->assertSessionHasErrors('email');
        Notification::assertNotSentTo(Crewmate::factory()->make(['email' => 'nobody@example.com']), PasswordResetRequestNotification::class);
    }

    public function testEmailIsRequired()
    {
        $response = $this->from($this->passwordEmailGetRoute())->post($this->passwordEmailPostRoute(), []);

        $response->assertRedirect($this->passwordEmailGetRoute());
        $response->assertSessionHasErrors('email');
    }
}
