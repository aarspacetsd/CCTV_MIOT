<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    // Membuat User Admin
    $adminUser = User::firstOrCreate(
      ['email' => 'ahmad@gmail.com'],
      [
        'name' => 'Administrator',
        'password' => Hash::make('password'),
      ]
    );
    // Memberikan role 'admin' ke user tersebut
    $adminUser->assignRole('admin');

    // Membuat User Editor
    $editorUser = User::firstOrCreate(
      ['email' => 'editor12@example.com'],
      [
        'name' => 'Editor',
        'password' => Hash::make('password'),
      ]
    );
    $editorUser->assignRole('editor');
  }
}
