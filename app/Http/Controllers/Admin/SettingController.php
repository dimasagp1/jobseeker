<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache; // TAMBAHKAN INI

class SettingController extends Controller
{
    public function index()
    {
        $company = Company::first();

        if (!$company) {
            $company = Company::create([
                'company_name' => 'HerbaTech Job Portal',
                'industry' => 'Healthcare & Technology',
                'is_active' => true
            ]);
        }

        return view('admin.settings.index', compact('company'));
    }

    public function update(Request $request)
    {
        $company = Company::firstOrFail();

        $validated = $request->validate([
            'company_name'        => 'required|string|max:255',
            'company_description' => 'nullable|string',
            'company_logo'        => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'favicon'             => 'nullable|image|mimes:ico,png,jpg,jpeg|max:1024',
            'facebook'            => 'nullable|url|max:255',
            'twitter'             => 'nullable|url|max:255',
            'linkedin'            => 'nullable|url|max:255',
            'instagram'           => 'nullable|url|max:255',
            'company_profile_url' => 'nullable|url|max:255',
            'hero_title'          => 'nullable|string|max:255',
            'hero_description'    => 'nullable|string',
            'hero_image'          => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'hero_cta_text'       => 'nullable|string|max:50',
            'register_title'      => 'nullable|string|max:255',
            'register_description'=> 'nullable|string',
            'guest_banner_title'  => 'nullable|string|max:255',
            'guest_banner_description' => 'nullable|string',
            'guest_banner_image'  => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'industry'            => 'nullable|string|max:255',
            'company_size'        => 'nullable|integer|min:1',
            'company_website'     => 'nullable|url|max:255',
        ]);

        if ($request->hasFile('company_logo')) {
            if ($company->company_logo) {
                Storage::disk('public')->delete($company->company_logo);
            }
            $validated['company_logo'] = $request->file('company_logo')->store('company/logos', 'public');
        }

        if ($request->hasFile('favicon')) {
            if ($company->favicon) {
                Storage::disk('public')->delete($company->favicon);
            }
            $validated['favicon'] = $request->file('favicon')->store('company/favicons', 'public');
        }

        if ($request->hasFile('hero_image')) {
            if ($company->hero_image) {
                Storage::disk('public')->delete($company->hero_image);
            }
            $validated['hero_image'] = $request->file('hero_image')->store('company/hero', 'public');
        }

        if ($request->hasFile('guest_banner_image')) {
            if ($company->guest_banner_image) {
                Storage::disk('public')->delete($company->guest_banner_image);
            }
            $validated['guest_banner_image'] = $request->file('guest_banner_image')->store('company/auth', 'public');
        }

        $company->update($validated);

        // BERSIHKAN CACHE SETIAP KALI SETTING DIUPDATE
        Cache::forget('global_site_settings');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan identitas portal berhasil diperbarui.');
    }
}