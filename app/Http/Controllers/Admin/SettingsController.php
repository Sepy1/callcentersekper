<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function editSla()
    {
        $sla = Setting::getValue('sla_days', 2);
        $categories = \App\Models\Category::orderBy('name')->get();
        return view('admin.settings.sla', ['sla_days' => (int)$sla, 'categories' => $categories]);
    }

    public function updateSla(Request $request)
    {
        $data = $request->validate([
            'sla_days' => 'required|integer|min:1|max:365',
        ]);

        Setting::setValue('sla_days', $data['sla_days'], 'integer');

        return redirect()->back()->with('success', 'SLA updated');
    }
}
