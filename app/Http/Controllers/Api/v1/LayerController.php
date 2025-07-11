<?php

namespace App\Http\Controllers\Api\v1;
use App\Http\Controllers\Controller; // ✅ Add this
   use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\AdminLayer;
use App\Services\LayerTableService;


class LayerController extends Controller
{

    public function store(Request $request)
        {
            $request->validate([
                'type' => 'required|string|in:country,state,district,river,road',
                'geojson' => 'required|array',
            ]);

            $geojson = $request->geojson;
            $type = strtolower($request->type);
            $tableName = 'adm_layer_' . $type;

            $inserted = [];
            $skipped = [];

            // 1. Check admin layer
            $adminLayer = AdminLayer::where('table_name', $tableName)->first();

            if (!$adminLayer) {
                // 2. If not exists, create table
                if (!Schema::hasTable($tableName)) {
                    app(LayerTableService::class)->createLayerTable($tableName, 'polygon'); // or line/point
                }

                // 3. Create new admin layer record
                AdminLayer::create([
                    'name' => ucfirst($type),

                    'description' => ucfirst($type) . ' layer',
                    'table_name' => $tableName,
                    'is_active' => true,
                ]);
            } else {
                // 4. Optional: update layer if needed (optional)
                $adminLayer->update([
                    'description' => ucfirst($type) . ' layer updated',
                    'is_active' => true,
                ]);
            }

            // 5. Insert or skip features
            foreach ($geojson['features'] as $feature) {
                $props = $feature['properties'] ?? null;
                $geometry = $feature['geometry'] ?? null;

                if (!$props || !$geometry) {
                    $skipped[] = ['reason' => 'Missing properties or geometry'];
                    continue;
                }

                $name = $props['name'] ?? null;
                $code = $props['code'] ?? null;

                if (!$name || !$code) {
                    $skipped[] = ['reason' => 'Missing name or code'];
                    continue;
                }

                $exists = DB::table($tableName)
                    ->where('name', $name)
                    ->orWhere('code', $code)
                    ->first();

                if ($exists) {
                    $skipped[] = ['name' => $name, 'code' => $code, 'reason' => 'Already exists'];
                    continue;
                }

                try {
                   DB::table($tableName)->insert([
                        'name' => $name,
                        'code' => $code,
                        'status' => 'active',
                        'geometry' => DB::raw("ST_GeomFromGeoJSON('" . json_encode($geometry) . "')"),
                        'properties' => json_encode($props), // ✅ Store all properties here
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $inserted[] = ['name' => $name, 'code' => $code];
                } catch (\Exception $e) {
                    $skipped[] = [
                        'name' => $name,
                        'code' => $code,
                        'reason' => 'DB error: ' . $e->getMessage()
                    ];
                }
            }

            return response()->json([
                'message' => 'Processed GeoJSON features.',
                'table' => $tableName,
                'inserted' => $inserted,
                'skipped' => $skipped,
            ]);
        }


  public function fetch(Request $request)
{
    $request->validate([
        'type' => 'required|string|in:country,state,district,river,road',
    ]);

    $type = strtolower($request->type);
    $tableName = 'adm_layer_' . $type;

    if (!Schema::hasTable($tableName)) {
        return response()->json(['error' => 'Layer table not found'], 404);
    }

    $records = DB::table($tableName)
        ->select('name', 'code', 'status', DB::raw('ST_AsGeoJSON(geometry) as geojson'))
        ->get();

    $features = $records->map(function ($item) {
        return [
            'type' => 'Feature',
            'properties' => [
                'name' => $item->name,
                'code' => $item->code,
                'status' => $item->status,
            ],
            'geometry' => json_decode($item->geojson),
        ];
    });

    return response()->json($this->utf8ize([
        'type' => 'FeatureCollection',
        'features' => $features,
    ]));
}

private function utf8ize($mixed)
{
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = $this->utf8ize($value); // Use $this-> here since it's inside a class
        }
    } elseif (is_string($mixed)) {
        return mb_convert_encoding($mixed, 'UTF-8', 'UTF-8');
    }
    return $mixed;
}

}
