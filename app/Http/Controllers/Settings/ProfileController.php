<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Contracts\DeletesUsers;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use App\Models\User;
use App\Teams\Teams;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Actions\ConfirmPassword;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ProfileController
{
  /**
   * Show the user's profile settings page.
   */
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
  public function update(ProfileUpdateRequest $request): RedirectResponse
  {
    /** @var User $user */
    $user = $request->user();

    $photo = $request->file('photo');
    if ($photo instanceof UploadedFile) {
      $user->updateProfilePhoto($photo);
    }

    /** @var array<string, mixed> $safeData */
    $safeData = $request->safe()->only(['name', 'email']);
    $user->fill($safeData);

    if ($user->isDirty('email')) {
      $user->email_verified_at = null;
    }

    $user->save();

    return to_route('profile.edit');
  }

  /**
   * Delete the user's profile.
   */
  public function destroy(Request $request, StatefulGuard $guard): HttpResponse
  {
    // Check if account deletion is allowed
    abort_unless(Teams::hasAccountDeletionFeatures(), HttpResponse::HTTP_FORBIDDEN);

    /** @var User $user */
    $user = $request->user();

    /** @var ConfirmPassword $confirmPassword */
    $confirmPassword = resolve(ConfirmPassword::class);

    /** @var string|null $password */
    $password = $request->password;

    $confirmed = $confirmPassword(
      $guard, $user, $password
    );

    if (! $confirmed) {
      throw ValidationException::withMessages([
        'password' => __('The password is incorrect.'),
      ]);
    }

    /** @var User $freshUser */
    $freshUser = $user->fresh();

    /** @var DeletesUsers $deleter */
    $deleter = resolve(DeletesUsers::class);
    $deleter->delete($freshUser);

    $guard->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return Inertia::location(url('/'));
  }
}
