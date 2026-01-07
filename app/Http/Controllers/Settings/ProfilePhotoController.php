<?php

namespace App\Http\Controllers\Settings;

use App\Teams\Teams;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class ProfilePhotoController extends Controller
{
  /**
   * Delete the current user's profile photo.
   *
   * @return RedirectResponse
   */
  public function destroy(Request $request)
  {
    if (! Teams::managesProfilePhotos()) {
      abort(Response::HTTP_FORBIDDEN);
    }

    $request->user()->deleteProfilePhoto();

    return back(303)->with('status', 'profile-photo-deleted');
  }
}
