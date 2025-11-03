<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

/**
 * @method void invite(User $user, Model $team, string $email, string $role = null)
 */
interface InvitesTeamMembers
{
  //
}
