<?php

namespace App\Teams;

class Features
{
  /**
   * Determine if the given feature is enabled.
   */
  public static function enabled(string $feature): bool
  {
    /** @var array<int, string> $features */
    $features = config('teams.features', []);

    return in_array($feature, $features);
  }

  /**
   * Determine if the feature is enabled and has a given option enabled.
   */
  public static function optionEnabled(string $feature, string $option): bool
  {
    return static::enabled($feature) &&
           config("teams-options.{$feature}.{$option}") === true;
  }

  /**
   * Determine if the application is allowing profile photo uploads.
   */
  public static function managesProfilePhotos(): bool
  {
    return static::enabled(static::profilePhotos());
  }

  /**
   * Determine if the application is using any API features.
   */
  public static function hasApiFeatures(): bool
  {
    return static::enabled(static::api());
  }

  /**
   * Determine if the application is using any team features.
   */
  public static function hasTeamFeatures(): bool
  {
    return static::enabled(static::teams());
  }

  /**
   * Determine if invitations are sent to team members.
   */
  public static function sendsTeamInvitations(): bool
  {
    return static::optionEnabled(static::teams(), 'invitations');
  }

  /**
   * Determine if the application has terms of service / privacy policy confirmation enabled.
   */
  public static function hasTermsAndPrivacyPolicyFeature(): bool
  {
    return static::enabled(static::termsAndPrivacyPolicy());
  }

  /**
   * Determine if the application is using any account deletion features.
   */
  public static function hasAccountDeletionFeatures(): bool
  {
    return static::enabled(static::accountDeletion());
  }

  /**
   * Enable the profile photo upload feature.
   */
  public static function profilePhotos(): string
  {
    return 'profile-photos';
  }

  /**
   * Enable the API feature.
   */
  public static function api(): string
  {
    return 'api';
  }

  /**
   * Enable the teams feature.
   *
   * @param  array<string, mixed>  $options
   */
  public static function teams(array $options = []): string
  {
    if ($options !== []) {
      config(['teams-options.teams' => $options]);
    }

    return 'teams';
  }

  /**
   * Enable the terms of service and privacy policy feature.
   */
  public static function termsAndPrivacyPolicy(): string
  {
    return 'terms';
  }

  /**
   * Enable the account deletion feature.
   */
  public static function accountDeletion(): string
  {
    return 'account-deletion';
  }
}
