<?php
namespace App\Validation\Rules;

use App\Models\User;
use Respect\Validation\Rules\AbstractRule;

class PasswordStrength extends AbstractRule
{
  public  function validate($input)
  {
    return strlen($input) > 7;
  }
}
