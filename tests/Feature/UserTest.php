<?php namespace Tests\Feature;

use App\Role;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class UserTest
 * @package Tests\Feature
 */
class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic user details request.
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
            ->assertExactJson([
                'created_at' => $user->created_at . "",
                'id' => $user->id,
                'md5' => $user->md5,
                'name' => $user->name,
                'roles' => [],
                'updated_at' => $user->updated_at . "",
            ]);
    }

    /**
     * A user details request with roles in response.
     * @return void
     */
    public function testCanGetAuthenticatedUserWithRoles()
    {
        $user = $this->createPostManager();
        $team = $this->createTeam([$user]);

        $response = $this
            ->actingAs($user, 'api')
            ->get('/api/users/user');

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'created_at' => $user->created_at . "",
                'id' => $user->id,
                'md5' => $user->md5,
                'name' => $user->name,
                'roles' => [['key' => Role::MANAGE_POSTS]],
                'team_roles' => [$team->id => [['key' => Role::MANAGE_COMPETITIONS]]],
                'updated_at' => $user->updated_at . "",
            ]);
    }

    /**
     * A basic user search request.
     * @return void
     */
    public function testCanFindUserByName()
    {
        $users = factory(User::class, 3)->create();
        $searchFor = $users[0];

        $response = $this->get('/api/users/search?term=' . $searchFor->name);

        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [[
                    'name' => $searchFor->name,
                    'id' => $searchFor->id,
                ]],
            ]);
    }

    /**
     * A basic user registration request.
     * @return void
     */
    public function testCanRegisterNewUser()
    {
        $response = $this->json('post', '/api/users/', [
            'email' => 'tahq69@gmail.com',
            'name' => 'Igors krasjukovs',
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'name' => 'Igors krasjukovs',
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Igors krasjukovs',
        ]);
    }

    /**
     * Failing user registration request.
     * @return void
     */
    public function testRegistrationFailsOnInvalidPswConfirmation()
    {
        $response = $this->json('post', '/api/users/', [
            'email' => 'tahq69@gmail.com',
            'name' => 'Igors krasjukovs',
            'password' => 'secret',
            'password_confirmation' => '!secret_',
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'password' => ['The password confirmation does not match.']
                ]
            ]);

        $this->assertDatabaseMissing('users', [
            'name' => 'Igors krasjukovs'
        ]);
    }
}
