<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test a bad register attempty
     *
     * @return void
     */
    public function testBadRegister()
    {
        $response = $this->postJson('auth/register', [])
            ->seeStatusCode(400);


    }

    /**
     * Test a bad register attempt
     *
     * @return void
     */
    public function testBadEmail()
    {
        $this->post('auth/register', [
            'name' => $this->faker()->userName,
            'email' => 'test',
            'password' => $this->faker()->password(6, 20),
        ])->seeStatusCode(400);
    }

    /**
     * Test a good register attempt
     *
     * @return void
     */
    public function testSuccessfulRegister()
    {
        $username = $this->faker()->userName;
        $email = $this->faker()->email;

        $this->post('auth/register', [
            'name' => $username,
            'email' => $email,
            'password' => $this->faker()->password(6, 20),
        ])
            ->seeStatusCode(200) // should be 201
            ->seeJson()
            ->seeInDatabase('users', [
                'name' => $username,
                'email' => $email
            ]);

        var_dump($this->response->getContent());
        die();
    }
}
