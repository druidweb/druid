<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Models\User;
use App\Teams\Teams;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

final class ProfilePhotoController extends Controller
{
  /**
   * Delete the current user's profile photo.
   */
  public function destroy(Request $request): RedirectResponse
  {
    abort_unless(Teams::managesProfilePhotos(), Response::HTTP_FORBIDDEN);

    /** @var User $user */
    $user = $request->user();
    $user->deleteProfilePhoto();

    return back(303)->with('status', 'profile-photo-deleted');
  }
}
