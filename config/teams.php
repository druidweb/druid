<?php

declare(strict_types=1);

use App\Http\Middleware\AuthenticateSession;
use App\Teams\Features;

return [

  /**
   * DRUID TEAMS STACK
   *
   * Druid Teams uses Vue and Inertia.js exclusively for the frontend.
   * This value is fixed and should not be changed.
   */
  'stack' => 'inertia',

  /**
   * ROUTE MIDDLEWARE
   *
   * Here you may specify which middleware will be assigned to the routes
   * that are registered with the application. When necessary, you may modify
   * these middleware; however, this default value is usually sufficient.
   */
  'middleware' => ['web'],
  'auth_session' => AuthenticateSession::class,

  /**
   * AUTHENTICATION GUARD
   *
   * Here you may specify the authentication guard that will be used while
   * authenticating users. This value should correspond with one of your
   * guards that is already present in your "auth" configuration file.
   */
  'guard' => 'sanctum',

  /**
   * FEATURES
   *
   * Some features are optional. You may disable features by removing them
   * from this array. You're free to only remove some of these features or
   * you can even remove all of these if you need to.
   */
  'features' => [
    Features::termsAndPrivacyPolicy(),
    Features::profilePhotos(),
    Features::api(),
    Features::teams(['invitations' => true]),
    Features::accountDeletion(),
  ],

  /**
   * PROFILE PHOTO DISK
   *
   * This configuration value determines the default disk that will be used
   * when storing profile photos for your application's users. Typically
   * this will be the "public" disk but you may adjust this if needed.
   */
  'profile_photo_disk' => 'public',
];
