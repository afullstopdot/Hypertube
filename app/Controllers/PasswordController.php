<?php

namespace App\Controllers;

use App\Models\User;
use App\Controllers\Controller;
use Respect\Validation\Validator as v;

class PasswordController extends Controller
{
  public function postReset($request, $response)
  {
    $validation = $this->validator->validate($request, [
      'email' => v::noWhitespace()->notEmpty()->email()->resetEmailExists(),
    ]);

    if ($validation->failed()) {
      return $response->withRedirect($this->router->pathFor('auth.reset'));
    }
    // if we get here, the e-mail exists, continue with verification.
    $user_info = $this->container->profile->getUserByEmail($request->getParam('email'));
    if (isset($user_info[0]))
      $user_info = $user_info[0];
    else
      $user_info = array('name' => 'HyperUser', 'first_name' => 'xxxxxxxxx');
    // create token for validation.
    $hash = md5(rand(0,1000));
    if (isset($hash))
      $_SESSION['hash_tok'] = $hash;
    // update database
    $this->container->user->setVerification($request->getParam('email'), $hash);
    // create the auth_url we send via e-mail
    $auth_uri = 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/hypertube/home/auth/reset?id=' . $hash;
    // sending html , so update the headers.
    $to = $request->getParam('email');
    $subject = 'Reset Account';
    $headers = "From: " . 'afullstopdot19@gmail.com' . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $message = '<html>
    <head>
      <style>
        .l-button {
          width: 80%;
          margin-left: auto;
          margin-right: auto;
          background-color: blue;
        }
        a:link, a:visited {
          background-color: white;
          color: black;
          border: 2px solid green;
          padding: 10px 20px;
          text-align: left;
          text-decoration: none;
          display: inline-block;
        }
        a:hover, a:active {
          background-color: green;
          color: white;
        }
        p {
          font-color: gold;
          font-style: bold;
          text-align: center;
        }
      </style>
    </head>
    <body>
      <div class="container-fluid">
      <p>Hi '. $user_info['first_name'] .' Your account: ' . $user_info['name'] . ' recieved a request to change the password</p>
      <p>If this is true please click the link below, otherwise ignore this e-mail, thanks.</p>
      <div class="l-button">
        <p><a href="' . $auth_uri . '">Reset Account</a></p>
      </div>
      <p>If the link does not work please click this one --> ' . $auth_uri . '</p>
      </div>
      <hr>
      <p>Thank You, dotube admin.</p>
    </body>
    </html>';
    mail($to, $subject, $message, $headers);
    return $response->withRedirect($this->router->pathFor('home'));
  }

  public function getReset($request, $response)
  {
    if (!empty($request->getParam('id')))
    {
      if ($this->container->user->getVerification($request->getParam('id')))
      {
        $this->container->user->setVerification($request->getParam('email'), NULL);
        return $this->view->render($response, 'render/reset-change.twig');
      }
    }
    else
      return $this->view->render($response, 'render/reset.twig');
  }

  public  function  postResetChangePassword($request, $response)
  {
    $validation = $this->validator->validate($request, [
      'new_password' => v::noWhitespace()->notEmpty()->passwordStrength(),
      'cnew_password'=> v::noWhitespace()->notEmpty()->passwordStrength(),
    ]);

    if ($validation->failed()) {
      return $response->withRedirect($this->router->pathFor('auth.reset.change'));
    }
    if (isset($_SESSION['hash_tok'])) {
      $this->container->profile->resetChangePassword($request->getParam('new_password'), $_SESSION['hash_tok']);
      unset($_SESSION['hash_tok']);
      $this->flash->addMessage('info', 'Your password was changed');
    }
    else {
      $this->flash->addMessage('error', 'Your password was not changed');
    }
    return $response->withRedirect($this->router->pathFor('home'));
  }

  public function getChangePassword($request, $response)
  {
    return $this->view->render($response, 'render/change.twig');
  }

  public function postChangePassword($request, $response)
  {
    $validation = $this->validator->validate($request, [
      'password_old' => v::noWhitespace()->notEmpty()->MatchesPassword($this->auth->user()->password),
      'password' => v::noWhitespace()->notEmpty(),
    ]);

    if ($validation->failed()) {
      return $response->withRedirect($this->router->pathFor('auth.password.change'));
    }

    $this->auth->user()->setPassword($request->getParam('password'));
    $this->flash->addMessage('info', 'Your password was changed');
    return $response->withRedirect($this->router->pathFor('home'));
  }
}
