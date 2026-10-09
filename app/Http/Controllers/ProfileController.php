<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index');
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return back()->with(
            'success',
            'Informasi akun berhasil diperbarui.'
        );
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with(
            'success',
            'Password berhasil diperbarui.'
        );
    }

    public function updatePhoto(Request $request)
    {
        $validated = $request->validate([
            'avatar' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        $user = $request->user();

        $oldPhoto = $user->avatar_path;

        $path = $request->file('avatar')->store(
            'profile-photos',
            'public'
        );

        $user->update([
            'avatar_path' => $path,
        ]);

        if ($oldPhoto) {
            Storage::disk('public')->delete($oldPhoto);
        }

        return back()->with(
            'success',
            'Foto profil berhasil diperbarui.'
        );
    }

    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'theme_preference' => [
                'required',
                Rule::in(['light', 'dark']),
            ],
            'email_notifications' => [
                'sometimes',
                'boolean',
            ],
        ]);

        $request->user()->update([
            'theme_preference' => $validated['theme_preference'],
            'email_notifications' => $request->boolean(
                'email_notifications'
            ),
        ]);

        return back()->with(
            'success',
            'Preferensi berhasil disimpan.'
        );
    }
}