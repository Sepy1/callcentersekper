<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KonfirmasiWaba;

class KonfirmasiWabaController extends Controller
{
    /**
     * Update waba field by phone number (hp).
     */
    public function updateByHp(Request $request)
    {
        $data = $request->validate([
            'hp' => 'required|string|max:50',
            'waba' => 'required|string|max:255',
        ]);

        $record = KonfirmasiWaba::where('hp', $data['hp'])->first();

        if (! $record) {
            // create a new record with hp and waba, leave other fields null
            $record = KonfirmasiWaba::create([
                'hp' => $data['hp'],
                'waba' => $data['waba'],
            ]);

            return response()->json(['success' => true, 'data' => $record], 201);
        }

        $record->waba = $data['waba'];
        $record->save();

        return response()->json(['success' => true, 'data' => $record]);
    }
}
