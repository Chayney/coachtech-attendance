<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_email()
    {
        $user = User::factory()->create([
            'name' => 'テスト',
            'email' => 'test@test.com',
            'password' => bcrypt('password')
        ]);
        $loginData = [
            'password' => 'password'
        ];
        $response = $this->post(route('login'), $loginData);
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
        $this->assertGuest();
        $user->delete();   
    }

    public function test_password()
    {
        $user = User::factory()->create([
            'name' => 'テスト',
            'email' => 'test@test.com',
            'password' => bcrypt('password')
        ]);
        $loginData = [
            'email' => 'test@test.com'
        ];
        $response = $this->post(route('login'), $loginData);
        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
        $this->assertGuest();
        $user->delete();   
    }

    public function test_not_exist()
    {
        $user = User::factory()->create([
            'name' => 'テスト',
            'email' => 'test@test.com',
            'password' => bcrypt('password')
        ]);
        $loginData = [
            'email' => 'user@test.com'
        ];
        $response = $this->post(route('login'), $loginData);
        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません']);
        $this->assertGuest();
        $user->delete();   
    }
}
