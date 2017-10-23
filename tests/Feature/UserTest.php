<?php namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class UserTest
 * @package Tests\Feature
 */
class UserTest extends TestCase
{
    /**
     * A basic test example.
     * @return void
     */
    public function testCanGetAuthenticatedUser()
    {
        $user = factory(User::class)->create();
        $response = $this
            ->actingAs($user, 'api')
            ->get('/api/users/user');

        $response
            ->assertStatus(200)
            ->assertJson([
                'email' => $user->email,
                'roles' => []
            ]);
    }
}
