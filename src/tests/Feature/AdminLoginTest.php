<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminLoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_admin_email()
    {
        if (!Role::where('name', 'admin')->exists()) {
            Role::create(['name' => 'admin']);
        }
        $user = User::factory()->create([
            'name' => 'テスト',
            'email' => 'admin@test.com',
            'password' => bcrypt('password')
        ]);
        $user->assignRole('admin');
        $loginData = [
            'password' => 'password'
        ];
        $response = $this->post(route('login'), $loginData);
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
        $this->assertGuest();
        $user->delete();
    }

    public function test_admin_password()
    {
        if (!Role::where('name', 'admin')->exists()) {
            Role::create(['name' => 'admin']);
        }
        $user = User::factory()->create([
            'name' => 'テスト',
            'email' => 'admin@test.com',
            'password' => bcrypt('password')
        ]);
        $user->assignRole('admin');
        $loginData = [
            'email' => 'admin@test.com'
        ];
        $response = $this->post(route('login'), $loginData);
        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
        $this->assertGuest();
        $user->delete();
    }

    public function test_admin_not_exist()
    {
        if (!Role::where('name', 'admin')->exists()) {
            Role::create(['name' => 'admin']);
        }
        $user = User::factory()->create([
            'name' => 'テスト',
            'email' => 'admin@test.com',
            'password' => bcrypt('password')
        ]);
        $user->assignRole('admin');
        $loginData = [
            'email' => 'user@test.com'
        ];
        $response = $this->post(route('login'), $loginData);
        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません']);
        $this->assertGuest();
        $user->delete();
    }
}
