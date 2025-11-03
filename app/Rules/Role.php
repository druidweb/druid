<?php

namespace App\Rules;

use App\Teams\Teams;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Role implements ValidationRule
{
  /**
   * Run the validation rule.
   */
  public function validate(string $attribute, mixed $value, Closure $fail): void
  {
    if (! in_array($value, array_keys(Teams::$roles))) {
      $fail(__('The :attribute must be a valid role.'));
    }
  }
}
