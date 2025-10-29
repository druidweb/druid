<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Requests\Settings\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\RouteDiscovery\Attributes\Route;

class ProfileController
{
  /**
   * Show the user's profile settings page.
   */
  #[Route('settings/profile', name: 'profile.edit', middleware: ['auth'])]
  public function edit(Request $request): Response
  {
    return Inertia::render('settings/Profile', [
      'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
      'status' => $request->session()->get('status'),
    ]);
  }

  /**
   * Update the user's profile information.
   */
  #[Route('settings/profile', name: 'profile.update', middleware: ['auth'])]
  public function update(ProfileUpdateRequest $request): RedirectResponse
  {
    /** @var User $user */
    $user = $request->user();
    $user->fill($request->validated());

    if ($user->isDirty('email')) {
      $user->email_verified_at = null;
    }

    $user->save();

    return to_route('profile.edit');
  }

  /**
   * Delete the user's profile.
   */
  #[Route('settings/profile', name: 'profile.destroy', middleware: ['auth'])]
  public function destroy(Request $request): RedirectResponse
  {
    $request->validate([
      'password' => ['required', 'current_password'],
    ]);

    /** @var User $user */
    $user = $request->user();

    Auth::logout();

    $user->delete();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
  }
}
