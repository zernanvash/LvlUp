<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(protected CloudinaryService $cloudinary) {}

    /**
     * Display a public user profile.
     */
    public function show(string $username): View
    {
        $user = User::where('name', $username)->firstOrFail();

        if (!$user->is_public && (!Auth::check() || Auth::id() !== $user->id)) {
            abort(403, 'This profile is private.');
        }

        $user->load([
            'equippedBadges',
            'projects' => function ($query) {
                $query->where('is_featured', true)->latest()->take(6);
            },
            'unlockedNodes.skill',
        ]);

        $stats = [
            'level'          => $user->level,
            'xp'             => $user->xp,
            'rank'           => $user->rank,
            'total_xp'       => $user->total_xp,
            'total_projects' => $user->projects()->count(),
            'total_badges'   => $user->badges()->count(),
            'unlocked_nodes' => $user->unlockedNodes()->count(),
        ];

        return view('profile.public', compact('user', 'stats'));
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Upload / replace the user's profile photo via Cloudinary.
     */
    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $user = $request->user();

        // Delete old photo from Cloudinary if it exists
        if ($user->avatar && str_contains($user->avatar, 'cloudinary.com')) {
            $this->cloudinary->deleteByUrl($user->avatar);
        }

        $url = $this->cloudinary->uploadProfilePhoto($request->file('photo'), $user->id);

        $user->update(['avatar' => $url]);

        return Redirect::route('profile.edit')->with('status', 'photo-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Toggle the user's profile visibility.
     */
    public function toggleVisibility(Request $request): RedirectResponse
    {
        $request->user()->toggleVisibility();

        return Redirect::route('profile.edit')->with('status', 'visibility-updated');
    }
}
