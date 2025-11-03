<?php

namespace App\Teams;

class Features
{
  /**
   * Determine if the given feature is enabled.
   */
  public static function enabled(string $feature): bool
  {
    return in_array($feature, config('teams.features', []));
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
   *
   * @return bool
   */
  public static function managesProfilePhotos()
  {
    return static::enabled(static::profilePhotos());
  }

  /**
   * Determine if the application is using any API features.
   *
   * @return bool
   */
  public static function hasApiFeatures()
  {
    return static::enabled(static::api());
  }

  /**
   * Determine if the application is using any team features.
   *
   * @return bool
   */
  public static function hasTeamFeatures()
  {
    return static::enabled(static::teams());
  }

  /**
   * Determine if invitations are sent to team members.
   *
   * @return bool
   */
  public static function sendsTeamInvitations()
  {
    return static::optionEnabled(static::teams(), 'invitations');
  }

  /**
   * Determine if the application has terms of service / privacy policy confirmation enabled.
   *
   * @return bool
   */
  public static function hasTermsAndPrivacyPolicyFeature()
  {
    return static::enabled(static::termsAndPrivacyPolicy());
  }

  /**
   * Determine if the application is using any account deletion features.
   *
   * @return bool
   */
  public static function hasAccountDeletionFeatures()
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
