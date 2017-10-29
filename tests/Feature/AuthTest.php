<?php namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class AuthTest
 * @package Tests\Feature
 */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test of reset password email.
     * @return void
     */
    public function testCanSendResetPasswordEmail()
    {
        Mail::fake();

        $users = factory(User::class, 3)->create();
        $user = $users[0];

        $response = $this->json('post', '/api/password/email', [
            'email' => $user->email,
        ]);

        $response
            ->assertStatus(200)
            ->assertJson(['status' => 'We have e-mailed your password reset link!']);
    }

    /**
     * A basic test of reset password.
     * @return void
     */
    public function testCanResetPassword()
    {
        $users = factory(User::class, 3)->create();
        $user = $users[0];

        $response = $this->json('post', '/api/password/reset', [
            'email' => $user->email,
            'password' => 'secret',
            'password_confirmation' => 'secret',
            'token' => '123546',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson(['a' => 2]);
    }
}
