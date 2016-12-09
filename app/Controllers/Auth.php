<?php

namespace App\Controllers;

use App\Models\User;

class Auth
{

  public function user()
  {
    return User::find($_SESSION['user']);
  }

  public function check()
  {
    return isset($_SESSION['user']);
  }

  public function oauth_attempt($email)
  {
    $user = User::where('email', $email)->first();
    if (!$user) {
      return false;
    }
    $_SESSION['user'] = $user->id;
    return (true);
  }

  public function attempt($email, $password, $meth)
  {
    if ($meth == 0)
    {
      $user = User::where('email', $email)->first();
      if (!$user) {
        return false;
      }
    }
    if ($meth == 1)
    {
      $user = User::where('name', $email)->first();
      if (!$user) {
        return false;
      }
    }

    if (password_verify($password, $user->password))
    {
      $_SESSION['user'] = $user->id;
      return (true);
    }

    return false;
  }

  public function logout()
  {
    unset($_SESSION['user']);
  }
}
