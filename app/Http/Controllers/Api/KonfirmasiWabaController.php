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
            return response()->json(['success' => false, 'message' => 'Record not found for given hp'], 404);
        }

        $record->waba = $data['waba'];
        $record->save();

        return response()->json(['success' => true, 'data' => $record]);
    }
}
