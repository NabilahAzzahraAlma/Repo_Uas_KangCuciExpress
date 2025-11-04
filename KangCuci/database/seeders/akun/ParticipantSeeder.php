<?php

namespace Database\Seeders\akun;

use Carbon\Carbon;
use App\Models\User;
use App\Models\participant;
use App\Models\Cabang;
use App\Models\Detailparticipant;
use Illuminate\Database\Seeder;

class participantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cabang = Cabang::where('id', 1)->first();
        $cabang2 = Cabang::where('id', 2)->onlyTrashed()->first();
        $roleparticipant = 'participant';

        $keluargaparticipant = participant::create([
            'kartu_keluarga' => '1234567890123456',
            'alamat' => 'Keowneran Karawang, Jabar',
            'rt' => 4,
            'rw' => 1,
        ]);

        //? Cabang 1
        $this->createparticipant($cabang, $keluargaparticipant, $roleparticipant, 1, ['nama' => 'Jual Pecel', 'gaji' => 1000000]);
        $this->createparticipant($cabang, $keluargaparticipant, $roleparticipant, 3, ['nama' => 'Antar Jemput', 'gaji' => 1000000]);
        $this->createparticipant($cabang, $keluargaparticipant, $roleparticipant, 4, ['nama' => '-', 'gaji' => 0]);

        //? Cabang 2
        $participant2 = User::factory()->create([
            'username' => 'participant 2',
            'email' => 'participant2@gmail.com',
            'cabang_id' => $cabang2->id,
            'deleted_at' => Carbon::now(),
        ]);
        $participant2->assignRole($roleparticipant);
        Detailparticipant::create([
            'nama' => 'participant 2',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Surabaya',
            'tanggal_lahir' => '1999-01-01',
            'telepon' => '084234567892',
            'alamat' => 'Keowneran Karawang, Jabar',
            'participant_id' => $keluargaparticipant->id,
            'user_id' => $participant2->id,
        ]);
    }

    public function createparticipant($cabang, $keluargaparticipant, $roleparticipant, $angka, $pemasukkan)
    {
        $participant = User::factory()->create([
            'username' => 'participant ' . $angka,
            'email' => 'participant' . $angka . '@gmail.com',
            'cabang_id' => $cabang->id,
        ]);
        $participant->assignRole($roleparticipant);
        Detailparticipant::create([
            'nama' => 'participant ' . $angka,
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Surabaya',
            'tanggal_lahir' => '1999-01-01',
            'telepon' => '08423456789' . $angka,
            'alamat' => 'Keowneran Karawang, Jabar',
            'nama_pemasukkan' => $pemasukkan['nama'],
            'pemasukkan' => $pemasukkan['gaji'],
            'participant_id' => $keluargaparticipant->id,
            'user_id' => $participant->id,
        ]);
    }
}
