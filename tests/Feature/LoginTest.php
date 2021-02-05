<?php

namespace DavidSchneider\LaravelCrewmate\Tests\Feature;

use DavidSchneider\LaravelCrewmate\Crewmate;
use DavidSchneider\LaravelCrewmate\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function successfulLoginRoute()
    {
        return route('crewmate.home');
    }

    protected function loginGetRoute()
    {
        return route('crewmate.login');
    }

    protected function loginPostRoute()
    {
        return route('crewmate.login');
    }

    protected function logoutRoute()
    {
        return route('crewmate.logout');
    }

    protected function successfulLogoutRoute()
    {
        return url('/');
    }

    protected function guestMiddlewareRoute()
    {
        return route('crewmate.home');
    }

    protected function getTooManyLoginAttemptsMessage()
    {
        return sprintf('/^%s$/', str_replace('\:seconds', '\d+', preg_quote(__('auth.throttle'), '/')));
    }

    public function testUserCanViewALoginForm()
    {
        $response = $this->get($this->loginGetRoute());
        $response->assertSuccessful();
        $response->assertViewIs('crewmate::auth.login');
    }

    public function testUserCannotViewALoginFormWhenAuthenticated()
    {
        $this->be(Crewmate::factory()->create(), 'crewmate');
        $response = $this->get($this->loginGetRoute());
        $response->assertRedirect($this->guestMiddlewareRoute());
    }

    public function testUserCanLoginWithCorrectCredentials()
    {
        $user = Crewmate::factory()->create([
            'password' => bcrypt($password = 'i-love-laravel'),
        ]);
        $response = $this->post($this->loginPostRoute(), [
            'email' => $user->email,
            'password' => $password,
        ]);
        $response->assertRedirect($this->successfulLoginRoute());
        $this->assertAuthenticatedAs($user, 'crewmate');
    }

    public function testRememberMeFunctionality()
    {
        $user = Crewmate::factory()->create([
            'id' => random_int(1, 100),
            'password' => Hash::make($password = 'i-love-laravel'),
        ]);

        $response = $this->post($this->loginPostRoute(), [
            'email' => $user->email,
            'password' => $password,
            'remember' => 'on',
        ]);

        $user = $user->fresh();

        $response->assertRedirect($this->successfulLoginRoute());
        $response->assertCookie(Auth::guard('crewmate')->getRecallerName(), vsprintf('%s|%s|%s', [
            $user->id,
            $user->getRememberToken(),
            $user->password,
        ]));
        $this->assertAuthenticatedAs($user, 'crewmate');
    }

    public function testUserCannotLoginWithIncorrectPassword()
    {
        $user = Crewmate::factory()->create([
            'password' => Hash::make('i-love-laravel'),
        ]);

        $response = $this->from($this->loginGetRoute())->post($this->loginPostRoute(), [
            'email' => $user->email,
            'password' => 'invalid-password',
        ]);

        $response->assertRedirect($this->loginGetRoute());
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }

    public function testUserCannotLoginWithEmailThatDoesNotExist()
    {
        $response = $this->from($this->loginGetRoute())->post($this->loginPostRoute(), [
            'email' => 'nobody@example.com',
            'password' => 'invalid-password',
        ]);

        $response->assertRedirect($this->loginGetRoute());
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }

    public function testUserCanLogout()
    {
        $this->be(Crewmate::factory()->create(), 'crewmate');
        $this->assertAuthenticated();

        $response = $this->post($this->logoutRoute());

        $response->assertRedirect($this->successfulLogoutRoute());
        $this->assertGuest();
    }

    public function testUserCannotLogoutWhenNotAuthenticated()
    {
        $response = $this->post($this->logoutRoute());

        $response->assertRedirect($this->loginGetRoute());
        $this->assertGuest();
    }

    public function testUserCannotMakeMoreThanFiveAttemptsInOneMinute()
    {
        $user = Crewmate::factory()->create([
            'password' => Hash::make($password = 'i-love-laravel'),
        ]);

        foreach (range(0, 5) as $_) {
            $response = $this->from($this->loginGetRoute())->post($this->loginPostRoute(), [
                'email' => $user->email,
                'password' => 'invalid-password',
            ]);
        }

        $response->assertRedirect($this->loginGetRoute());
        $response->assertSessionHasErrors('email');
        $this->assertMatchesRegularExpression(
            $this->getTooManyLoginAttemptsMessage(),
            collect(
                $response
                    ->baseResponse
                    ->getSession()
                    ->get('errors')
                    ->getBag('default')
                    ->get('email')
            )->first()
        );
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }
}
