<?php

namespace Database\Seeders\akun;

use App\Models\Owner;
use App\Models\User;
use Illuminate\Database\Seeder;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleOwner = 'owner';

        $owner = User::factory()->create([
            'username' => 'Owner',
            'email' => 'owner@gmail.com',
        ]);
        $owner->assignRole($roleOwner);
        Owner::create([
            'nama' => 'Owner 1',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Surabaya',
            'tanggal_lahir' => '1970-01-01',
            'telepon' => '081234567890',
            'alamat' => 'Keowneran Karawang, Jabar',
            'user_id' => $owner->id,
        ]);
    }
}
