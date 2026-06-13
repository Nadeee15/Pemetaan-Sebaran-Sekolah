<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SekolahController extends Controller
{
    /**
     * Halaman utama - menampilkan peta SIG
     */
    public function index()
    {
        // Statistik untuk dashboard
        $stats = $this->getStatistik();
        return view('peta.index', compact('stats'));
    }

    /**
     * API: ambil semua data sekolah sebagai GeoJSON untuk Leaflet
     */
    public function getGeoJSON(Request $request)
    {
        $query = Sekolah::query()
            ->whereNotNull('lat')
            ->whereNotNull('long')
            ->where('lat', '!=', 0)
            ->where('long', '!=', 0)
            ->whereRaw('"long" NOT IN (\'NaN\'::double precision, \'Infinity\'::double precision, \'-Infinity\'::double precision)')
            ->whereRaw('lat NOT IN (\'NaN\'::double precision, \'Infinity\'::double precision, \'-Infinity\'::double precision)');

        // Filter jenjang
        if ($request->stage && $request->stage !== 'all') {
            $query->where('stage', $request->stage);
        }

        // Filter status
        if ($request->status && $request->status !== 'all') {
            $statusInput = strtolower($request->status);
            if ($statusInput === 'negeri') {
                $query->where(function($q) {
                    $q->where('status', 'N')->orWhere('status', 'ilike', '%negeri%');
                });
            } elseif ($statusInput === 'swasta') {
                $query->where(function($q) {
                    $q->where('status', 'S')->orWhere('status', 'ilike', '%swasta%');
                });
            } else {
                $query->where('status', 'ilike', '%' . $request->status . '%');
            }
        }

        // Filter kondisi fasilitas
        if ($request->kondisi && $request->kondisi !== 'all') {
            $query->where('kondisi_fasilitas', $request->kondisi);
        }

        // Search nama sekolah
        if ($request->search) {
            $query->where('school_name', 'ilike', '%' . $request->search . '%');
        }

        $sekolah = $query->get(['id_sekolah', 'school_name', 'stage', 'status', 'city_name', 'district_name', 'lat', 'long', 'kondisi_fasilitas']);

        $features = $sekolah->filter(function ($s) {
            $lat = (float)$s->lat;
            $lng = (float)$s->long;
            return is_finite($lat) && is_finite($lng) && !is_nan($lat) && !is_nan($lng);
        })->values()->map(function ($s) {
            $statusStr = $s->status;
            if ($statusStr === 'N') $statusStr = 'Negeri';
            elseif ($statusStr === 'S') $statusStr = 'Swasta';

            return [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float)$s->long, (float)$s->lat],
                ],
                'properties' => [
                    'id' => $s->id_sekolah,
                    'name' => $s->school_name,
                    'stage' => $s->stage,
                    'status' => $statusStr,
                    'city' => $s->city_name,
                    'district' => $s->district_name,
                    'lat' => (float)$s->lat,
                    'lng' => (float)$s->long,
                    'kondisi_fasilitas' => $s->kondisi_fasilitas ?? '-',
                ],
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
            'total' => $features->count(),
        ]);
    }

    /**
     * API: radius layanan sekolah (PostGIS ST_DWithin)
     */
    public function getRadius(Request $request)
    {
        $lat = $request->lat;
        $lng = $request->lng;
        $radius = $request->radius ?? 1000; // meter
        $schoolId = $request->school_id;

        if (!$lat || !$lng) {
            return response()->json(['error' => 'Koordinat tidak valid'], 422);
        }

        // Gunakan ST_DWithin dengan geography untuk akurasi meter
        $sekolah = DB::select("
            SELECT id_sekolah, school_name, stage, status, city_name, district_name, lat, long,
                   ST_Distance(
                       geom::geography,
                       ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography
                   ) as jarak_meter
            FROM sekolah
            WHERE ST_DWithin(
                geom::geography,
                ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography,
                ?
            )
            ORDER BY jarak_meter ASC
        ", [$lng, $lat, $lng, $lat, $radius]);

        return response()->json([
            'center' => ['lat' => (float)$lat, 'lng' => (float)$lng],
            'radius' => (int)$radius,
            'count' => count($sekolah),
            'sekolah' => $sekolah,
        ]);
    }

    /**
     * API: statistik data sekolah
     */
    public function getStatistikApi()
    {
        return response()->json($this->getStatistik());
    }

    /**
     * Helper: ambil semua statistik
     */
    private function getStatistik(): array
    {
        $total = Sekolah::count();

        $perJenjang = Sekolah::select('stage', DB::raw('count(*) as jumlah'))
            ->groupBy('stage')
            ->orderBy('jumlah', 'desc')
            ->pluck('jumlah', 'stage')
            ->toArray();

        $perStatus = Sekolah::select(DB::raw("
                CASE 
                    WHEN status = 'N' OR LOWER(status) LIKE '%negeri%' THEN 'Negeri'
                    WHEN status = 'S' OR LOWER(status) LIKE '%swasta%' THEN 'Swasta'
                    ELSE status 
                END as status_group
            "), DB::raw('count(*) as jumlah'))
            ->groupBy('status_group')
            ->pluck('jumlah', 'status_group')
            ->toArray();

        $perKota = Sekolah::select('city_name', DB::raw('count(*) as jumlah'))
            ->groupBy('city_name')
            ->orderBy('jumlah', 'desc')
            ->pluck('jumlah', 'city_name')
            ->toArray();

        $perKondisi = Sekolah::select('kondisi_fasilitas', DB::raw('count(*) as jumlah'))
            ->whereNotNull('kondisi_fasilitas')
            ->groupBy('kondisi_fasilitas')
            ->pluck('jumlah', 'kondisi_fasilitas')
            ->toArray();

        return [
            'total' => $total,
            'per_jenjang' => $perJenjang,
            'per_status' => $perStatus,
            'per_kota' => $perKota,
            'per_kondisi' => $perKondisi,
        ];
    }

    /**
     * API: list kota untuk filter
     */
    public function getKota()
    {
        $kota = Sekolah::select('city_name')
            ->distinct()
            ->orderBy('city_name')
            ->pluck('city_name');

        return response()->json($kota);
    }

    /**
     * API: data sekolah + bantuan
     */
    public function getBantuan(Request $request)
    {
        $query = DB::table('sekolah')
            ->join('bantuan_sekolah', 'sekolah.id_sekolah', '=', 'bantuan_sekolah.id_sekolah')
            ->whereNotNull('sekolah.lat')
            ->whereNotNull('sekolah.long');
            
        if ($request->jenis_bantuan && $request->jenis_bantuan !== 'all') {
            $query->where('bantuan_sekolah.jenis_bantuan', $request->jenis_bantuan);
        }
        
        if ($request->status_bantuan && $request->status_bantuan !== 'all') {
            $query->where('bantuan_sekolah.status_bantuan', $request->status_bantuan);
        }
        
        if ($request->tingkat_prioritas && $request->tingkat_prioritas !== 'all') {
            $query->where('bantuan_sekolah.tingkat_prioritas', $request->tingkat_prioritas);
        }

        $bantuan = $query->get();

        $features = $bantuan->map(function ($b) {
            return [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float)$b->long, (float)$b->lat],
                ],
                'properties' => [
                    'id_sekolah' => $b->id_sekolah,
                    'id_bantuan' => $b->id_bantuan,
                    'school_name' => $b->school_name,
                    'stage' => $b->stage,
                    'jenis_bantuan' => $b->jenis_bantuan,
                    'jumlah' => $b->jumlah,
                    'status_bantuan' => $b->status_bantuan,
                    'tingkat_prioritas' => $b->tingkat_prioritas,
                    'keterangan' => $b->keterangan,
                    'lat' => (float)$b->lat,
                    'lng' => (float)$b->long,
                ],
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    /**
     * API: prioritas bantuan dari view_prioritas_bantuan
     */
    public function getPrioritasBantuan(Request $request)
    {
        $prioritas = DB::table('view_prioritas_bantuan')->get();
        return response()->json($prioritas);
    }

    /**
     * API: jalur bantuan dari view_jalur_bantuan
     */
    public function getJalurBantuan(Request $request)
    {
        $jalur = DB::table('view_jalur_bantuan')->get();
        return response()->json($jalur);
    }
}
