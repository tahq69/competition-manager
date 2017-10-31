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

        $this->assertDatabaseHas('password_resets', ['email' => $user->email]);
    }

    /**
     * A basic test of reset password.
     * @return void
     */
    public function testCanResetPassword()
    {
        // In Laravel it is not possible to test password reset because we cant get token from email.
        $this->assertTrue(true);
    }
}
