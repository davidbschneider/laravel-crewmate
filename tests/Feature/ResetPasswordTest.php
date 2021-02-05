<?php

namespace DavidSchneider\LaravelCrewmate\Tests\Feature;

use DavidSchneider\LaravelCrewmate\Crewmate;
use DavidSchneider\LaravelCrewmate\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    protected function getValidToken($user)
    {
        $token = Str::random(64);
        DB::table('crewmate_password_resets')->insert([
            'email' => $user->email,
            'token' => $token
        ]);
        return $token;
    }

    protected function getInvalidToken()
    {
        return 'invalid-token';
    }

    protected function passwordResetGetRoute($token)
    {
        return  route('crewmate.password.reset', $token);
    }

    protected function passwordResetPostRoute()
    {
        return  route('crewmate.password.update');
    }

    protected function successfulPasswordResetRoute()
    {
        return  route('crewmate.login');
    }

    public function testUserCanViewAPasswordResetForm()
    {
        $user = Crewmate::factory()->create();

        $response = $this->get($this->passwordResetGetRoute($token = $this->getValidToken($user)));

        $response->assertSuccessful();
        $response->assertViewIs('crewmate::auth.passwords.reset');
        $response->assertViewHas('token', $token);
    }

    public function testUserCanViewAPasswordResetFormWhenAuthenticated()
    {
        $user = Crewmate::factory()->create();

        $response = $this->actingAs($user)->get($this->passwordResetGetRoute($token = $this->getValidToken($user)));

        $response->assertSuccessful();
        $response->assertViewIs('crewmate::auth.passwords.reset');
        $response->assertViewHas('token', $token);
    }

    public function testUserCanResetPasswordWithValidToken()
    {
        Event::fake();
        $user = Crewmate::factory()->create();

        $response = $this->post($this->passwordResetPostRoute(), [
            'token' => $this->getValidToken($user),
            'email' => $user->email,
            'password' => 'new-awesome-password',
            'password_confirmation' => 'new-awesome-password',
        ]);

        $response->assertRedirect($this->successfulPasswordResetRoute());
        $this->assertEquals($user->email, $user->fresh()->email);
        $this->assertTrue(Hash::check('new-awesome-password', $user->fresh()->password));
        $this->assertGuest();
    }

    public function testUserCannotResetPasswordWithInvalidToken()
    {
        $user = Crewmate::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $response = $this->post($this->passwordResetPostRoute(), [
            'token' => $this->getInvalidToken(),
            'email' => $user->email,
            'password' => 'new-awesome-password',
            'password_confirmation' => 'new-awesome-password',
        ]);

        $this->assertEquals($user->email, $user->fresh()->email);
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
        $this->assertGuest();
    }

    public function testUserCannotResetPasswordWithoutProvidingANewPassword()
    {
        $user = Crewmate::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $response = $this->from($this->passwordResetGetRoute($token = $this->getValidToken($user)))->post($this->passwordResetPostRoute(), [
            'token' => $token,
            'email' => $user->email,
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertRedirect($this->passwordResetGetRoute($token));
        $response->assertSessionHasErrors('password');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertEquals($user->email, $user->fresh()->email);
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
        $this->assertGuest();
    }

    public function testUserCannotResetPasswordWithoutProvidingAnEmail()
    {
        $user = Crewmate::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $response = $this->from($this->passwordResetGetRoute($token = $this->getValidToken($user)))->post($this->passwordResetPostRoute(), [
            'token' => $token,
            'email' => '',
            'password' => 'new-awesome-password',
            'password_confirmation' => 'new-awesome-password',
        ]);

        $response->assertRedirect($this->passwordResetGetRoute($token));
        $response->assertSessionHasErrors('email');
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertEquals($user->email, $user->fresh()->email);
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
        $this->assertGuest();
    }
}
