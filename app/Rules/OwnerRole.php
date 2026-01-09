<?php

declare(strict_types=1);

namespace App\Rules;

use App\Teams\Role;

final class OwnerRole extends Role
{
  /**
   * Create a new owner role instance.
   */
  public function __construct()
  {
    parent::__construct('owner', 'Owner', ['*']);
  }
}
