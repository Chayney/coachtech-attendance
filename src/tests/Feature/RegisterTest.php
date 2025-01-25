<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class RegisterTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_name()
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];
        $response = $this->post('/register', $data);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['name' => 'お名前を入力してください']);
    }

    public function test_email()
    {
        $data = [
            'name' => 'テスト',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];
        $response = $this->post('/register', $data);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    public function test_password_min8()
    {
        $data = [
            'name' => 'テスト',
            'email' => 'test@example.com',
            'password' => 'pass',
            'password_confirmation' => 'pass',
        ];
        $response = $this->post('/register', $data);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください']);
    }

    public function test_password_confirmed()
    {
        $data = [
            'name' => 'テスト',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'wordpass',
        ];
        $response = $this->post('/register', $data);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['password' => 'パスワードと一致しません']);
    }

    public function test_password()
    {
        $data = [
            'name' => 'テスト',
            'email' => 'test@example.com',
        ];
        $response = $this->post('/register', $data);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    public function test_user_can_register()
    {
        $data = [
            'name' => 'テスト',
            'email' => 'test@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];
        $response = $this->post(route('register'), $data);
        $this->assertDatabaseHas('users', [
            'email' => 'test@test.com',
        ]);
        $response->assertRedirect('/attendance');
        User::where('email', 'test@test.com')->delete();
        $this->assertDatabaseMissing('users', [
            'email' => 'test@test.com',
        ]);
    }
}
