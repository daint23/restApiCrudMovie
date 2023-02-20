<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MovieTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * @group testregister
     */

    public function test_required_field_register()
    {
        $this->postJson('api/register', ['Accept' => 'application/json'])
                ->assertStatus(422)
                ->assertJson([
                    "message"=> "The name field is required. (and 2 more errors)",
                    "errors"=> [
                        "name"=> [
                            "The name field is required."
                        ],
                        "email" => [
                            "The email field is required."
                        ],
                        "password" => [
                            "The password field is required."
                        ]
                    ]
                ]);        
    }

    /**
     * @group testregister
     */
    public function test_repeat_password()
    {
        $userData = [
            "name" => "Rahmad",
            "email" => "rahmadiswad@gmail.com",
            "password" => "secret123456"
        ];

        $this->postJson('api/register', $userData, ['Accept' => 'application/json'])
                ->assertStatus(422)
                ->assertJson([
                    "message" => "The password field confirmation does not match.",
                    "errors" => [
                        "password" => [
                            "The password field confirmation does not match."
                        ]
                    ]
                ]);
    }

    /**
     * @group testregister
     */
    public function test_success_register()
    {
        $userData = [
            "name" => "Rahmad",
            "email" => "rahmadiswad@gmail.com",
            "password" => "secret123456",
            "password_confirmation" => "secret123456"
        ];

        $this->postJson('api/register', $userData, ['Accept' => 'application/json'])
                ->assertStatus(201)
                ->assertJsonStructure([
                    "data" => [
                        "name",
                        "email",
                        "updated_at",
                        "created_at",
                        "id"
                    ],
                    "token",
                    "token_type"
                ]);
    }

    /**
     * @group testlogin
     */

    public function test_required_field_login()
    {
        $this->postJson('api/login', ['Accept' => 'application/json'])
                ->assertStatus(422)
                ->assertJson([
                    "message" => "The email field is required. (and 1 more error)",
                    "errors" => [
                        "email" => [
                            "The email field is required."
                        ],
                        "password" => [
                            "The password field is required."
                        ]
                    ]
                ]);
    }

    /**
     * @group testlogin
     */
    public function test_invalid_data()
    {
        $inputData = [
            'email' => 'rahmadiswadi@gmail.comp',
            'password' => 'secret123456'
        ];

        $this->postJson('api/login', $inputData, ['Accept' => 'application/json'])
                ->assertStatus(422)
                ->assertJson([
                    "message" => "Invalid Credentials"
                ]);
    }

    /**
     * @group testlogin
     */
    public function test_success_login()
    {
        $inputData = [
            'email' => 'rahmadiswad@gmai.com',
            'password' => 'secret123456'
        ];
        $this->postJson('api/login', $inputData, ['Accept' => 'application/json'])
                ->assertStatus(200)
                ->assertJsonStructure([
                    "message",
                    "token",
                    "token_type"
                ]);
    }
}
