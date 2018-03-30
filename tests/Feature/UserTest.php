<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    public function testDeleteSuccess()
    {
        $user = User::find(1);
        $response = $this->delete('api/v1/users/' . $user->id);
        $response->assertStatus(204);
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function testDelete404()
    {
        $user = User::orderBy('id', 'desc')->first();
        $notExistUser = $user->id + 1;
        $response = $this->delete('api/v1/users/' . $notExistUser);
        $response->assertStatus(404);
    }

    public function testCreateSuccess()
    {
        $postData = [
            'email' => 'myuniqueemail@gmail.com',
            'name' => 'Fisrt Last',
            'description' => 'This is my description',
            'status' => 1
        ];

        $response = $this->post('api/v1/users', $postData);
        $response->assertStatus(201);
        $this->assertDatabaseHas('users', $postData);
    }

    public function testCreateNotUniqueEmail()
    {
        $user = User::find(1);
        $postData = [
            'email' => $user->email,
            'name' => 'Fisrt Last',
            'description' => 'This is my description',
            'status' => 1
        ];

        $response = $this->post('api/v1/users', $postData);
        $response->assertStatus(422);
    }

    public function testCreateEmptyRequest()
    {
        $response = $this->post('api/v1/users', []);
        $response->assertStatus(422);
    }

    public function testUpdateSuccess()
    {
        $user = User::find(1);
        $data = [
            'name' => 'My new name',
        ];

        $response = $this->put('api/v1/users/' . $user->id, $data);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', array_merge($data, ['id' => $user->id]));
    }

    public function testUpdate404()
    {
        $user = User::orderBy('id', 'desc')->first();
        $notExistUser = $user->id + 1;
        $data = [
            'name' => 'My new name',
        ];

        $response = $this->put('api/v1/users/' . $notExistUser, $data);
        $response->assertStatus(404);
    }

    public function testUpdateNotUniqueEmail()
    {
        $userForData = User::orderBy('id', 'desc')->first();
        $userToUpdate = User::orderBy('id', 'asc')->first();
        $data = [
            'email' => $userForData->email,
        ];

        $response = $this->put('api/v1/users/' . $userToUpdate->id, $data);
        $response->assertStatus(422);
        $this->assertDatabaseHas('users', ['id' => $userToUpdate->id, 'email' => $userToUpdate->email]);
    }

    public function testGetUserById()
    {
        $user = User::find(1);
        $response = $this->get('api/v1/users/' . $user->id);
        $response->assertStatus(200);
    }

    public function testGetUserById404()
    {
        $user = User::orderBy('id', 'desc')->first();
        $notExistUser = $user->id + 1;
        $response = $this->get('api/v1/users/' . $notExistUser);
        $response->assertStatus(404);

    }
}
