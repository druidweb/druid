<?php

namespace App\Http\Controllers;

use App\Teams\Teams;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PrivacyPolicyController extends Controller
{
  /**
   * Show the privacy policy for the application.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Inertia\Response
   */
  public function show(Request $request)
  {
    $policyFile = Teams::localizedMarkdownPath('policy.md');

    return Inertia::render('PrivacyPolicy', [
      'policy' => Str::markdown(file_get_contents($policyFile)),
    ]);
  }
}
