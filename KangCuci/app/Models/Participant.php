<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $table = 'participant';  // keeping the table name same to avoid migration
    protected $primaryKey = 'id';
    public $incrementing = "true";
    public $timestamps = "true";
    protected $fillable = [
        'kartu_keluarga',
        'alamat',
        'rt',
        'rw',
    ];

    public function detailparticipant()
    {
        return $this->hasMany(Detailparticipant::class);
    }
}
