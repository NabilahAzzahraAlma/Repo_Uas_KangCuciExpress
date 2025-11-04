<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monitoringparticipant extends Model
{
    use HasFactory;

    protected $table = 'monitoring_participant';
    protected $primaryKey = 'id';
    public $incrementing = "true";
    public $timestamps = "true";
    protected $fillable = [
        'upah',
        'status',
        'bulan',
        'tahun',
        'detail_participant_id',
    ];

    public function participant()
    {
        return $this->belongsTo(Detailparticipant::class, 'detail_participant_id');
    }
}
