<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return Setting::all();
    }

    public function update(Request $request, Setting $setting)
    {
        $data = $request->validate([
            'valor' => 'required'
        ]);

        $setting->update($data);

        return $setting;
    }
}
