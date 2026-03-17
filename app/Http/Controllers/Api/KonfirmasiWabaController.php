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

        // normalize hp (e.g. +6285421xxx or 6285421xxx -> 085421xxx)
        $hp = $this->normalizeHp($data['hp']);

        $record = KonfirmasiWaba::where('hp', $hp)->first();

        if (! $record) {
            // create a new record with normalized hp and waba, leave other fields null
            $record = KonfirmasiWaba::create([
                'hp' => $hp,
                'waba' => $data['waba'],
            ]);

            return response()->json(['success' => true, 'data' => $record], 201);
        }

        $record->waba = $data['waba'];
        $record->save();

        return response()->json(['success' => true, 'data' => $record]);
    }

    /**
     * Normalize phone number to local format starting with 0.
     * Examples:
     *  - +6285421xxx -> 085421xxx
     *  - 6285421xxx  -> 085421xxx
     *  - 08123456    -> 08123456 (unchanged)
     */
    protected function normalizeHp(string $hp): string
    {
        // remove non-digit characters
        $digits = preg_replace('/\D+/', '', $hp);

        // remove leading plus if present (already removed by preg_replace but keep logic clear)
        $digits = ltrim($digits, '+');

        // if starts with country code 62, replace with 0
        if (strpos($digits, '62') === 0) {
            $digits = '0' . substr($digits, 2);
        }

        return $digits;
    }
}
