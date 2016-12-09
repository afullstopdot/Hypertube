<?php

namespace App\Controllers;
// use App\Auth\Auth;
use App\Models\User;
use \Slim\Views\Twig as View;
use Respect\Validation\Validator as v;

class ProfileController extends Controller
{
  public function changeProfileBio($request, $response)
  {
    $validation = $this->validator->validate($request, [
      'email' => v::noWhitespace()->notEmpty()->email(),
      'name' => v::noWhitespace()->notEmpty()->alpha(),
      'firstname' => v::noWhitespace()->notEmpty()->alpha(),
      'lastname' => v::noWhitespace()->notEmpty()->alpha(),
    ]);
    
    if ($validation->failed()) {
      $this->flash->addMessage('error', 'check settings, input failed validation.');
      return $response->withRedirect($this->router->pathFor('home.profile'));
    }
    // input validated, now we check so dont updated fields to the same value.s
    if ($this->auth->user()->getProfileEmail($this->auth->user()) !== ($request->getParam('email'))) {
      $this->auth->user()->setProfileEmail($request->getParam('email'));
    }
    if (($this->auth->user() !== $request->getParam('name'))) {
        $this->auth->user()->setProfileHandle($request->getParam('name'));
    }
    if ($this->auth->user()->getProfileFirstName($this->auth->user()) !== ($request->getParam('firstname'))) {
      $this->auth->user()->setProfileFirstName($request->getParam('firstname'));
    }
    if ($this->auth->user()->getProfileLastName($this->auth->user()) !== ($request->getParam('lastname'))) {
      $this->auth->user()->setProfileLastName($request->getParam('lastname'));
    }
    $this->flash->addMessage('info', 'BIO Updated.');
    return $response->withRedirect($this->router->pathFor('home.profile'));
  }

  public function changeProfilePicture($request, $response, $args)
  {
    $files = $request->getUploadedFiles();
    if (empty($files['newfile'])) {
      throw new Exception('Expected a newfile');
    }
    $file0 = $files['newfile'];
    if ($file0->getError() === UPLOAD_ERR_OK) {
      if (!file_exists($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'hypertube/home/uploads/' . $file0->getClientFilename()))
      {
        $ext = pathinfo($file0->getClientFilename(), PATHINFO_EXTENSION);
        $tempName = uniqid('profile-') . '.' . $ext;
        if ($ext === 'jpg' || $ext === 'png' || $ext === 'jpeg' || $ext === 'gif')
        {
          $path = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'hypertube/home/uploads/' . $tempName;
          $file0->moveTo($path);
          $this->auth->user()->setProfilePicture($tempName);
        }
        else
          $this->flash->addMessage('info', 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.');
      }
      else
        $this->auth->user()->setProfilePicture($file0->getClientFilename());
    }
    return $response->withRedirect($this->router->pathFor('home.profile'));
  }

  public function getProfile($request, $response)
  {
    return $this->view->render($response, 'render/profile.twig');
  }
}
