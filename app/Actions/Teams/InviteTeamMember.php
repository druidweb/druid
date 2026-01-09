<?php

declare(strict_types=1);

namespace App\Actions\Teams;

use App\Contracts\InvitesTeamMembers;
use App\Events\InvitingTeamMember;
use App\Mail\TeamInvitation;
use App\Models\Team;
use App\Models\User;
use App\Rules\Role;
use App\Teams\Teams;
use Closure;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

final class InviteTeamMember implements InvitesTeamMembers
{
  /**
   * Invite a new team member to the given team.
   */
  public function invite(User $user, Team $team, string $email, ?string $role = null): void
  {
    Gate::forUser($user)->authorize('addTeamMember', $team);

    $this->validate($team, $email, $role);

    event(new InvitingTeamMember($team, $email, $role));

    /** @var \App\Models\TeamInvitation $invitation */
    $invitation = $team->teamInvitations()->create([
      'email' => $email,
      'role' => $role,
    ]);

    Mail::to($email)->send(new TeamInvitation($invitation));
  }

  /**
   * Validate the invite member operation.
   */
  private function validate(Team $team, string $email, ?string $role): void
  {
    Validator::make([
      'email' => $email,
      'role' => $role,
    ], $this->rules($team), [
      'email.unique' => __('This user has already been invited to the team.'),
    ])->after(
      $this->ensureUserIsNotAlreadyOnTeam($team, $email)
    )->validateWithBag('addTeamMember');
  }

  /**
   * Get the validation rules for inviting a team member.
   *
   * @return array<string, \Illuminate\Contracts\Validation\Rule|array<int, mixed>|string>
   */
  private function rules(Team $team): array
  {
    return array_filter([
      'email' => [
        'required', 'email',
        Rule::unique(Teams::teamInvitationModel())->where(function (Builder $query) use ($team): void {
          $query->where('team_id', $team->id);
        }),
      ],
      'role' => Teams::hasRoles()
                      ? ['required', 'string', new Role]
                      : null,
    ]);
  }

  /**
   * Ensure that the user is not already on the team.
   */
  private function ensureUserIsNotAlreadyOnTeam(Team $team, string $email): Closure
  {
    return function (mixed $validator) use ($team, $email): void {
      /** @var \Illuminate\Validation\Validator $validator */
      $validator->errors()->addIf(
        $team->hasUserWithEmail($email),
        'email',
        __('This user already belongs to the team.')
      );
    };
  }
}
