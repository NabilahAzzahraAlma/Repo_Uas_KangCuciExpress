<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detailparticipant extends Model
{
    use HasFactory;

    protected $table = 'detail_participant';
    protected $primaryKey = 'id';
    public $incrementing = "true";
    public $timestamps = "true";
    protected $fillable = [
        'nama',
        'foto',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'telepon',
        'alamat',
        'mulai_kerja',
        'selesai_kerja',
        'nama_pemasukkan',
        'pemasukkan',
        'user_id',
        'participant_id',
    ];

    public function participant()
    {
        return $this->belongsTo(Participant::class, 'participant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function monitoringparticipant()
    {
        return $this->hasMany(Monitoringparticipant::class);
    }
}
