<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Rooms;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BasicTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testLandingPage()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function testRegisterPage()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function testRegisterMember()
    {
        $user = [
          'name' => 'Administrator',
          'email' => 'megatzulkhairi@gmail.com',
          'password' => 'Password123',
          'password_confirmation' => 'Password123'
        ];
    
        $response = $this->json('POST', '/register', $user);
        $response->assertStatus(201);
    }

    public function testLoginPage()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function testLoginMember()
    {
        $user = [
          'email' => 'megatzulkhairi@gmail.com',
          'password' => 'Password123x'
        ];
    
        $response = $this->json('POST', '/login', $user);
        $response->assertStatus(200);
    }

    public function testCreateRoom()
    {
        $room = [
            'name' => 'Harmony Chat Room',
            'desc' => 'lorem ipsum lorem ipsum lorem ipsum lorem ipsum',
            'owner' => '1',
        ];
        Rooms::create($room);

        $room = Rooms::find(1);
        $this->assertSame($room->name, $room['name']);
    }

    public function testCreateRoom2()
    {
        $room = [
            'name' => 'Harmony Chat Room 2',
            'desc' => 'lorem ipsum lorem ipsum lorem ipsum lorem ipsum',
            'owner' => '1',
        ];
        Rooms::create($room);

        $room = Rooms::find(2);
        $this->assertSame($room->name, $room['name']);
    }
}
