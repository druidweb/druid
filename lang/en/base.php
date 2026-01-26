<?php

return [

  /*
  |--------------------------------------------------------------------------
  | Common Actions
  |--------------------------------------------------------------------------
  */

  'actions' => [
    'add' => 'Add',
    'cancel' => 'Cancel',
    'close' => 'Close',
    'confirm' => 'Confirm',
    'continue' => 'Continue',
    'create' => 'Create',
    'delete' => 'Delete',
    'done' => 'Done',
    'leave' => 'Leave',
    'more' => 'More',
    'remove' => 'Remove',
    'save' => 'Save',
  ],

  /*
  |--------------------------------------------------------------------------
  | Status Messages
  |--------------------------------------------------------------------------
  */

  'status' => [
    'added' => 'Added.',
    'copied' => 'Copied!',
    'copy_to_clipboard' => 'Copy to clipboard',
    'created' => 'Created.',
    'saved' => 'Saved.',
    'enabled' => 'Enabled',
    'disabled' => 'Disabled',
    'last_used' => 'Last used',
  ],

  /*
  |--------------------------------------------------------------------------
  | Navigation
  |--------------------------------------------------------------------------
  */

  'nav' => [
    'dashboard' => 'Dashboard',
    'settings' => 'Settings',
    'settings_description' => 'Manage your profile and account settings',
    'documentation' => 'Documentation',
    'laracasts' => 'Laracasts',
    'navigation_menu' => 'Navigation Menu',
    'sidebar' => 'Sidebar',
    'toggle_sidebar' => 'Toggle Sidebar',
    'displays_mobile_sidebar' => 'Displays the mobile sidebar.',
    'platform' => 'Platform',
    'profile' => 'Profile',
    'two_factor' => 'Two-Factor Auth',
    'browser_sessions' => 'Browser Sessions',
    'appearance' => 'Appearance',
  ],

  /*
  |--------------------------------------------------------------------------
  | Welcome Page
  |--------------------------------------------------------------------------
  */

  'welcome' => [
    'title' => 'Welcome',
    'get_started' => "Let's get started",
    'ecosystem_intro' => 'Laravel has an incredibly rich ecosystem.',
    'ecosystem_suggest' => 'We suggest starting with the following.',
    'read_the' => 'Read the',
    'watch_tutorials_at' => 'Watch video tutorials at',
    'deploy_now' => 'Deploy now',
  ],

  /*
  |--------------------------------------------------------------------------
  | Authentication
  |--------------------------------------------------------------------------
  */

  'auth' => [
    'log_in' => 'Log in',
    'log_in_to_account' => 'Log in to your account',
    'log_out' => 'Log out',
    'sign_up' => 'Sign up',
    'register' => 'Register',
    'create_account' => 'Create an account',
    'create_account_description' => 'Enter your details below to create your account',
    'login_description' => 'Enter your email and password below to log in',
    'forgot_password' => 'Forgot password?',
    'forgot_password_description' => 'Enter your email to receive a password reset link',
    'reset_password' => 'Reset password',
    'reset_password_description' => 'Please enter your new password below',
    'remember_me' => 'Remember me',
    'confirm_password' => 'Confirm Password',
    'confirm_your_password' => 'Confirm your password',
    'secure_area' => 'This is a secure area of the application. Please confirm your password before continuing.',
    'or_return_to' => 'Or, return to',
    'welcome' => 'Welcome',
    'lets_get_started' => "Let's get started",
  ],

  /*
  |--------------------------------------------------------------------------
  | Email Verification
  |--------------------------------------------------------------------------
  */

  'verification' => [
    'title' => 'Email verification',
    'verify_email' => 'Verify email',
    'resend' => 'Resend verification email',
    'check_email' => 'Please verify your email address by clicking on the link we just emailed to you.',
  ],

  /*
  |--------------------------------------------------------------------------
  | Two-Factor Authentication
  |--------------------------------------------------------------------------
  */

  'two_factor' => [
    'title' => 'Two-Factor Authentication',
    'description' => 'Manage your two-factor authentication settings',
    'enable' => 'Enable 2FA',
    'enable_description' => 'Add an extra layer of security to your account by enabling two-factor authentication.',
    'enabled_description' => 'With two-factor authentication enabled, you will be prompted for a secure, random pin during login, which you can retrieve from the TOTP-supported application on your phone.',
    'disable' => 'Disable 2FA',
    'continue_setup' => 'Continue Setup',
    'regenerate_codes' => 'Regenerate Codes',
    'recovery_codes_title' => '2FA Recovery Codes',
    'recovery_codes_description' => 'Recovery codes let you regain access if you lose your 2FA device. Store them in a secure password manager.',
    'view_recovery_codes' => 'View Recovery Codes',
    'hide_recovery_codes' => 'Hide Recovery Codes',
    'recovery_codes_usage' => 'Each recovery code can be used once to access your account and will be removed after use. If you need more, click :link above.',
    'enter_recovery_code' => 'Enter recovery code',
    'recovery_code' => 'Recovery Code',
    'recovery_code_description' => 'Please confirm access to your account by entering one of your emergency recovery codes.',
    'auth_code' => 'Authentication Code',
    'auth_code_description' => 'Enter the authentication code provided by your authenticator application.',
    'use_auth_code' => 'login using an authentication code',
    'use_recovery_code' => 'login using a recovery code',
    'or_you_can' => 'or you can',
  ],

  /*
  |--------------------------------------------------------------------------
  | Profile & Account
  |--------------------------------------------------------------------------
  */

  'profile' => [
    'title' => 'Profile Information',
    'settings' => 'Profile settings',
    'description' => 'Update your name and email address',
    'manage_settings' => 'Manage your profile and account settings',
    'avatar' => 'Avatar',
    'select_photo' => 'Select New Photo',
    'remove_photo' => 'Remove Photo',
  ],

  /*
  |--------------------------------------------------------------------------
  | Password
  |--------------------------------------------------------------------------
  */

  'password' => [
    'title' => 'Update Password',
    'settings' => 'Password settings',
    'description' => 'Ensure your account is using a long, random password to stay secure',
    'current' => 'Current password',
    'new' => 'New password',
  ],

  /*
  |--------------------------------------------------------------------------
  | Sessions
  |--------------------------------------------------------------------------
  */

  'sessions' => [
    'title' => 'Browser Sessions',
    'description' => 'Manage and log out your active sessions on other browsers and devices',
    'info' => 'If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password.',
    'log_out_other' => 'Log Out Other Browser Sessions',
    'logout_other' => 'Log Out Other Browser Sessions',
    'log_out_confirm' => 'Please enter your password to confirm you would like to log out of your other browser sessions.',
    'this_device' => 'This device',
    'current_session' => 'Your current browser session',
  ],

  /*
  |--------------------------------------------------------------------------
  | Appearance
  |--------------------------------------------------------------------------
  */

  'appearance' => [
    'title' => 'Appearance settings',
    'description' => "Update your account's appearance settings",
  ],

  /*
  |--------------------------------------------------------------------------
  | Delete Account
  |--------------------------------------------------------------------------
  */

  'delete_account' => [
    'title' => 'Delete Account',
    'description' => 'Delete your account and all of its resources',
    'confirm' => 'Are you sure you want to delete your account?',
    'confirm_title' => 'Are you sure you want to delete your account?',
    'confirm_description' => 'Once your account is deleted, all of its resources and data will also be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.',
    'warning' => 'Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.',
  ],

  /*
  |--------------------------------------------------------------------------
  | Teams
  |--------------------------------------------------------------------------
  */

  'teams' => [
    'title' => 'Team Settings',
    'description' => "Manage your team's settings and members",
    'settings' => 'Team Settings',
    'settings_description' => "Manage your team's settings and members",
    'details' => 'Team Details',
    'details_description' => "The team's name and owner information.",
    'name' => 'Team Name',
    'name_description' => "The team's name and owner information.",
    'owner' => 'Team Owner',
    'role' => 'Role',
    'members' => 'Team Members',
    'members_description' => 'All of the people that are part of this team.',
    'add_member' => 'Add Team Member',
    'add_member_description' => 'Add a new team member to your team, allowing them to collaborate with you.',
    'add_member_email' => 'Please provide the email address of the person you would like to add to this team.',
    'remove_member' => 'Remove Team Member',
    'remove_member_confirm' => 'Are you sure you would like to remove this person from the team?',
    'pending_invitations' => 'Pending Team Invitations',
    'manage_role' => 'Manage Role',
    'manage_team' => 'Manage Team',
    'leave' => 'Leave Team',
    'leave_confirm' => 'Are you sure you would like to leave this team?',
    'switch' => 'Switch Teams',
    'switch_teams' => 'Switch Teams',
    'create' => 'Create Team',
    'create_new' => 'Create New Team',
    'create_description' => 'Create a new team to collaborate with others',
    'create_description_long' => 'Create a new team to collaborate with others on projects.',
    'delete' => 'Delete Team',
    'delete_description' => 'Permanently delete this team.',
  ],

  /*
  |--------------------------------------------------------------------------
  | API Tokens
  |--------------------------------------------------------------------------
  */

  'api_tokens' => [
    'title' => 'API Tokens',
    'description' => 'API tokens allow third-party services to authenticate with our application on your behalf.',
    'manage' => 'Manage API Tokens',
    'manage_description' => 'Manage your API tokens for third-party access',
    'create' => 'Create API Token',
    'created' => 'API Token Created',
    'created_description' => "Please copy your new API token. For your security, it won't be shown again.",
    'permissions' => 'API Token Permissions',
    'delete' => 'Delete API Token',
    'delete_confirm' => 'Are you sure you would like to delete this API token?',
    'delete_existing' => 'You may delete any of your existing tokens if they are no longer needed.',
  ],

  /*
  |--------------------------------------------------------------------------
  | Form Fields
  |--------------------------------------------------------------------------
  */

  'fields' => [
    'name' => 'Name',
    'full_name' => 'Full name',
    'email' => 'Email',
    'email_address' => 'Email address',
    'email_placeholder' => 'email@example.com',
    'password' => 'Password',
    'role' => 'Role',
    'permissions' => 'Permissions',
    'platform' => 'Platform',
  ],

  /*
  |--------------------------------------------------------------------------
  | Legal
  |--------------------------------------------------------------------------
  */

  'legal' => [
    'terms' => 'Terms of Service',
    'privacy' => 'Privacy Policy',
  ],

];
