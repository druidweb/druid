<?php

namespace App\Mail;

use App\Models\TeamInvitation as TeamInvitationModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class TeamInvitation extends Mailable
{
  use Queueable, SerializesModels;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct(
    /**
     * The team invitation instance.
     */
    public TeamInvitationModel $invitation
  ) {}

  /**
   * Build the message.
   */
  public function build(): static
  {
    return $this->markdown('emails.team-invitation', ['acceptUrl' => URL::signedRoute('team-invitations.accept', [
      'invitation' => $this->invitation,
    ])])->subject(__('Team Invitation'));
  }
}
