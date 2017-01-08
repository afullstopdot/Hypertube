<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = [
        'email',
        'name',
        'first_name',
        'last_name',
        'password',
        'profilePicture',
        'verification',
    ];

    public function getUserByEmail($email)
    {
      return $this->where('email', $email)->get(array('name', 'first_name', 'last_name'))->toArray();
    }

    public  function  getUser($name)
    {
      return $this->where('name', $name)->get(array('name', 'profilePicture', 'first_name', 'last_name', 'created_at'))->toArray();
    }

    public  function  getUsers()
    {
      return $this->get(array('name', 'profilePicture', 'first_name', 'last_name', 'created_at'))->toArray();
    }

    public  function  setProfileLastName($surname)
    {
      $this->update([
        'last_name' => $surname,
      ]);
    }

    public  function  getProfileLastName($user)
    {
      return $this->where('name', $user)->get(array('last_name'))->toArray();
    }

    public  function  setProfileFirstName($name)
    {
      $this->update([
        'first_name' => $name,
      ]);
    }

    public  function  getProfileFirstName($user)
    {
      return $this->where('name', $user)->get(array('first_name'))->toArray();
    }

    public  function  setProfileHandle($handle)
    {
      $this->update([
        'name' => $handle,
      ]);
    }

    public  function  getProfileEmail($user)
    {
      return $this->where('name', $user)->get(array('email'))->toArray();
    }

    public  function  setProfileEmail($email)
    {
        $this->update([
          'email' => $email,
        ]);
    }

    public  function    setProfilePicture($profile)
    {
        $this->update([
            'profilePicture' => $profile,
        ]);
    }
    // make sure this works - password controller
    public  function    resetChangePassword($password, $hash)
    {
      $this->where('verification', $hash)->update([
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'verification' => 'reset'
      ]);
    }

    public function setPassword($password)
    {
        $this->update([
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }

    public  function  getVerification($hash)
    {
        return $this->where('verification', $hash)->first();
    }

    public function setVerification($email, $hash)
    {
        $this->where('email', $email)->update([
          'verification' => $hash]
        );
    }
}
