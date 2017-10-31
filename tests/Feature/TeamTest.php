<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeamTest extends TestCase
{
    /**
     * A basic team list request.
     * @return void
     */
    public function testCanGetAuthenticatedUser()
    {
        $user = factory(\App\User::class)->states('super_admin')->create();
        $this->syncRole($user, \App\Role::SUPER_ADMIN);
        $teams = factory(\App\Team::class, 2)->create();

        $response = $this
            ->actingAs($user, 'api')
            ->get('/api/teams');

        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'name' => $teams[0]->name,
                        'short' => $teams[0]->short,
                        'id' => $teams[0]->id,
                    ], [
                        'name' => $teams[1]->name,
                        'short' => $teams[1]->short,
                        'id' => $teams[1]->id,
                    ],
                ],
            ]);
    }
}
