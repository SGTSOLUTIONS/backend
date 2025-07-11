<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CountryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'code' => 'required|string|size:3',
            'geojson' => 'required|json'
        ]);

        $country = DB::transaction(function () use ($validated) {
            return Country::create([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'boundary' => DB::raw("ST_GeomFromGeoJSON('{$validated['geojson']}')")
            ]);
        });

        return response()->json($country, 201);
    }

    public function show(Country $country)
    {
        return response()->json([
            'data' => $country->geoJson
        ]);
    }
}
