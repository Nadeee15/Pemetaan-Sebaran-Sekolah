<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    protected $table = 'sekolah';
    protected $primaryKey = 'id_sekolah';
    public $timestamps = false;

    protected $fillable = [
        'province_name',
        'city_name',
        'district_name',
        'school_name',
        'stage',
        'status',
        'lat',
        'long',
    ];

    protected $casts = [
        'lat' => 'float',
        'long' => 'float',
    ];

    /**
     * Scope filter by stage (jenjang)
     */
    public function scopeByStage($query, $stage)
    {
        if ($stage && $stage !== 'all') {
            return $query->where('stage', $stage);
        }
        return $query;
    }

    /**
     * Scope filter by status (negeri/swasta)
     */
    public function scopeByStatus($query, $status)
    {
        if ($status && $status !== 'all') {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope search by school name
     */
    public function scopeSearch($query, $keyword)
    {
        if ($keyword) {
            return $query->where('school_name', 'ilike', '%' . $keyword . '%');
        }
        return $query;
    }
}
