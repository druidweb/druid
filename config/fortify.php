<?php

declare(strict_types=1);

use Laravel\Fortify\Features;

return [

  /**
   * FORTIFY GUARD
   *
   * Here you may specify which authentication guard Fortify will use while
   * authenticating users. This value should correspond with one of your
   * guards that is already present in your "auth" configuration file.
   */
  'guard' => 'web',

  /**
   * FORTIFY PASSWORD BROKER
   *
   * Here you may specify which password broker Fortify can use when a user
   * is resetting their password. This configured value should match one
   * of your password brokers setup in your "auth" configuration file.
   */
  'passwords' => 'users',

  /**
   * USERNAME / EMAIL
   *
   * This value defines which model attribute should be considered as your
   * application's "username" field. Typically, this might be the email
   * address of the users but you are free to change this value here.
   *
   * Out of the box, Fortify expects forgot password and reset password
   * requests to have a field named 'email'. If the application uses
   * another name for the field you may define it below as needed.
   */
  'username' => 'email',
  'email' => 'email',

  /**
   * LOWERCASE USERNAMES
   *
   * This value defines whether usernames should be lowercased before saving
   * them in the database, as some database system string fields are case
   * sensitive. You may disable this for your application if necessary.
   */
  'lowercase_usernames' => true,

  /**
   * HOME PATH
   *
   * Here you may configure the path where users will get redirected during
   * authentication or password reset when the operations are successful
   * and the user is authenticated. You are free to change this value.
   */
  'home' => '/dashboard',

  /**
   * FORTIFY ROUTES PREFIX / SUBDOMAIN
   *
   * Here you may specify which prefix Fortify will assign to all the routes
   * that it registers with the application. If necessary, you may change
   * subdomain under which all of the Fortify routes will be available.
   */
  'prefix' => '',
  'domain' => null,

  /**
   * FORTIFY ROUTES MIDDLEWARE
   *
   * Here you may specify which middleware Fortify will assign to the routes
   * that it registers with the application. If necessary, you may change
   * these middleware but typically this provided default is preferred.
   */
  'middleware' => ['web'],

  /**
   * RATE LIMITING
   *
   * By default, Fortify will throttle logins to five requests per minute for
   * every email and IP address combination. However, if you would like to
   * specify a custom rate limiter to call then you may specify it here.
   */
  'limiters' => [
    'login' => 'login',
    'two-factor' => 'two-factor',
  ],

  /**
   * REGISTER VIEW ROUTES
   *
   * Here you may specify if the routes returning views should be disabled as
   * you may not need them when building your own application. This may be
   * especially true if you're writing a custom single-page application.
   */
  'views' => true,

  /**
   * FEATURES
   *
   * Some of the Fortify features are optional. You may disable the features
   * by removing them from this array. You're free to only remove some of
   * these features, or you can even remove all of these if you need to.
   */
  'features' => [
    Features::registration(),
    Features::resetPasswords(),
    Features::emailVerification(),
    Features::updateProfileInformation(),
    Features::updatePasswords(),
    Features::twoFactorAuthentication([
      'confirm' => true,
      'confirmPassword' => true,
      // 'window' => 0
    ]),
  ],
];
