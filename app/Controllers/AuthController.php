<?php

namespace App\Controllers;

use App\Models\User;
use Respect\Validation\Validator as v;

class AuthController extends Controller
{
  public function getOauthGoogleSignIn($request, $response)
  {
    $code = $request->getParam('code');
    $scope = $request->getParam('scope');
    // google passes error after redirect if credentials failed.
    if (!empty($request->getParam('scope'))) {
      $this->flash->addMessage('error', 'Error comunicating with Google.');
      return $response->withRedirect($this->router->pathFor('auth.signin'));
    }
    // recieved auth code, exchange for auth_token
    if (isset($code)) {
      $url = 'https://www.googleapis.com/oauth2/v4/token';
      $headers = array();
      $headers[] = 'Content-Type: application/x-www-form-urlencoded';
      $post = 'grant_type=authorization_code' .
      '&client_id=262198179634-6n3qa2c03nb9p73v66k2e6bu6o4rer55.apps.googleusercontent.com' .
      '&client_secret=np4z23leYuOM_cUj3UVZ8GWJ' .
      '&code=' . $code .
      '&redirect_uri=http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/hypertube/home/oauth/google/signin';
      // send post using curl
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

      $api_response = curl_exec($ch);

      curl_close($ch);
      unset($_SESSION['google_state']);
      $api_response = json_decode($api_response, true);
      // if api_response is set, google replied with a access token.
      if (isset($api_response) && !empty($api_response)) {
        $_SESSION['google_access_token'] = $api_response['access_token'];
        return $response->withRedirect($this->router->pathFor('oauth.google.signin'));
      }
      else {
        $this->flash->addMessage('info', 'Failed to communicated with Google.');
      }
      return $response->withRedirect($this->router->pathFor('auth.signin'));
    }

    // check if session variable is set, if not. pass the users to googles oauth to get a token.
    if (isset($_SESSION['google_access_token']) && $_SESSION['google_access_token']) {
      // use token to make api requests, retrieve info && create account.
      $url = 'https://www.googleapis.com/plus/v1/people/me';
      $authorization = 'Authorization: Bearer ' . $_SESSION['google_access_token'];
      $ch = curl_init($url);

      curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
      curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt( $ch, CURLOPT_HEADER, 0);
      curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

      $api_response = curl_exec($ch);
      curl_close($ch);

      $api_response = json_decode($api_response, true);
      unset($_SESSION['google_access_token']);

      if (isset($api_response)) {
        // authenticate user
        $auth = $this->auth->oauth_attempt(
          $api_response['emails'][0]['value']
        );
        if (!$auth) {
          $this->flash->addMessage('error', 'Not a valid account.');
          return $response->withRedirect($this->router->pathFor('auth.signin'));
        }
        $this->flash->addMessage('info', 'login successfull.');
        return $response->withRedirect($this->router->pathFor('home'));
      }
      else
      {
        $this->flash->addMessage('error', 'failed to communicate with google.');
        return $response->withRedirect($this->router->pathFor('auth.signup'));
      }
      return $response->withRedirect($this->router->pathFor('home'));
    }
    else {
      $_SESSION['google_state'] = hash('whirlpool', random_int(100, 999));
      $auth_url = 'https://accounts.google.com/o/oauth2/v2/auth?' .
      'response_type=code' .
      '&client_id=262198179634-6n3qa2c03nb9p73v66k2e6bu6o4rer55.apps.googleusercontent.com' .
      '&redirect_uri=http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/hypertube/home/oauth/google/signin' .
      '&scope=profile email' .
      '&state=' . $_SESSION['google_state'] .
      '&prompt=consent  select_account';
      return $response->withRedirect($auth_url);
    }
    return $response->withRedirect($this->router->pathFor('auth.signup'));
  }

  public function getOauthGoogleSignUp($request, $response)
  {
    $code = $request->getParam('code');
    $scope = $request->getParam('scope');
    // google passes error after redirect if credentials failed.
    if (!empty($request->getParam('scope'))) {
      $this->flash->addMessage('error', 'Error comunicating with Google.');
      return $response->withRedirect($this->router->pathFor('auth.signup'));
    }
    // recieved auth code, exchange for auth_token
    if (isset($code)) {
      $url = 'https://www.googleapis.com/oauth2/v4/token';
      $headers = array();
      $headers[] = 'Content-Type: application/x-www-form-urlencoded';
      $post = 'grant_type=authorization_code' .
      '&client_id=262198179634-6n3qa2c03nb9p73v66k2e6bu6o4rer55.apps.googleusercontent.com' .
      '&client_secret=np4z23leYuOM_cUj3UVZ8GWJ' .
      '&code=' . $code .
      '&redirect_uri=http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/hypertube/home/oauth/google/signup';
      // send post using curl
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

      $api_response = curl_exec($ch);

      curl_close($ch);
      unset($_SESSION['google_state']);
      $api_response = json_decode($api_response, true);
      // if api_response is set, google replied with a access token.
      if (isset($api_response) && !empty($api_response)) {
        $_SESSION['google_access_token'] = $api_response['access_token'];
        return $response->withRedirect($this->router->pathFor('oauth.google.signup'));
      }
      else {
        $this->flash->addMessage('info', 'Failed to communicated with Google.');
      }
      return $response->withRedirect($this->router->pathFor('auth.signup'));
    }

    // check if session variable is set, if not. pass the users to googles oauth to get a token.
    if (isset($_SESSION['google_access_token']) && $_SESSION['google_access_token']) {
      // use token to make api requests, retrieve info && create account.
      $url = 'https://www.googleapis.com/plus/v1/people/me';
      $authorization = 'Authorization: Bearer ' . $_SESSION['google_access_token'];
      $ch = curl_init($url);

      curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
      curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt( $ch, CURLOPT_HEADER, 0);
      curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

      $api_response = curl_exec($ch);
      curl_close($ch);

      $api_response = json_decode($api_response, true);
      if (isset($api_response)) {
        // use the info retrieved, create account in db if one doesnt exists already.
        $UserList = $this->container->profile->getUser($api_response['name']['givenName'] . '-' . $api_response['id']);
        $UserList2 = $this->container->profile->getUserByEmail($api_response['emails'][0]['value']);
        if (empty($UserList) && empty($UserList2)) {
          // i do no error checking, i just enter names in the db.
          $user = User::create([
            'name' => $api_response['name']['givenName'] . '-' . $api_response['id'],
            'email' => $api_response['emails'][0]['value'],
            'first_name' => $api_response['name']['givenName'],
            'last_name' => $api_response['name']['familyName'],
            'password' => 'N/A',
            'profilePicture' => 'joker-profile.png',
          ]);
          // the account is registered, i plan to make no api requests.
          $this->flash->addMessage('info', 'Google+ account created.');
          unset($_SESSION['google_access_token']);
        }
        else {
          $this->flash->addMessage('error', 'Google+ account exists.');
          unset($_SESSION['google_access_token']);
          return $response->withRedirect($this->router->pathFor('auth.signup'));
        }
      }
      else
        return $response->withRedirect($this->router->pathFor('auth.signup'));
      unset($_SESSION['google_access_token']);
      return $response->withRedirect($this->router->pathFor('home'));
    }
    else {
      $_SESSION['google_state'] = hash('whirlpool', random_int(100, 999));
      $auth_url = 'https://accounts.google.com/o/oauth2/v2/auth?' .
      'response_type=code&' .
      'client_id=262198179634-6n3qa2c03nb9p73v66k2e6bu6o4rer55.apps.googleusercontent.com&' .
      'redirect_uri=http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/hypertube/home/oauth/google/signup' . '&' .
      'scope=profile email&' .
      'state=' . $_SESSION['google_state'] . '&' .
      'prompt=consent  select_account';
      return $response->withRedirect($auth_url);
    }
    return $response->withRedirect($this->router->pathFor('auth.signup'));
  }

  public function getOauth42SignIn($request, $response)
  {
    // cant check results of functions if set
    $code = $request->getParam('code');
    $state = $request->getParam('state');
    if (isset($code) && isset($state))
    {
      // api request worked, check state then send post to 42 for auth code.
      if ($state === $_SESSION['state'])
      {
        // use token from code for auth_token
        $url = 'https://api.intra.42.fr/oauth/token';
        $post = 'grant_type=authorization_code' .
        '&client_id=9697f9df46513461e6ea0d6966e1298ded21bb4d58b3379d053d4cbff1e696f8' .
        '&client_secret=81ace918b83a8e304ddbd65083a4f5d68ce798c80f83551bc0850bdf58b90721' .
        '&code=' . $code .
        '&redirect_uri=' . 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/hypertube/home/oauth/42/signin' .
        '&state=' . $_SESSION['state'];
        // send post using curl
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $api_response = curl_exec($ch);
        curl_close($ch);
        $api_response = json_decode($api_response, true);
        if (isset($api_response['error'])) {
          $this->flash->addMessage('info', 'oauth failed, check client id etc.');
          return $response->withRedirect($this->router->pathFor('oauth.signin'));
        }
        else
          $_SESSION['42_access_token'] = $api_response['access_token'];
      }
      else
      {
        // the state is wrong means someone is trying to access illegally.
        echo 'CSF caught, fuck off.';
        die();
      }
      return $response->withRedirect($this->router->pathFor('oauth.42.signin'));
    }

    // use access token
    if (isset($_SESSION['42_access_token']) && $_SESSION['42_access_token'])
    {
      // 42 API responded with a token, so now get info bout user and register in db
      $url = 'https://api.intra.42.fr/v2/me';
      $authorization = 'Authorization: Bearer ' . $_SESSION['42_access_token'];
      $ch = curl_init( $url );
      curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
      curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt( $ch, CURLOPT_HEADER, 0);
      curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
      $api_response = curl_exec( $ch );
      curl_close($ch);
      $api_response = json_decode($api_response, true);

      if (isset($api_response['error'])) {

        unset ($_SESSION['42_access_token']);
        $this->flash->addMessage('error', 'Unable to communicate with 42.');
        return $response->withRedirect($this->router->pathFor('auth.signin'));

      }
      else {
        // retrieved info successfully, check if account exists.
        unset ($_SESSION['42_access_token']);
        if (isset($api_response)) {

          $UserList = $this->container->profile->getUser($api_response['login']);

          $auth = $this->auth->oauth_attempt(
            $api_response['email']
          );
          if (!$auth) {
            $this->flash->addMessage('error', 'Not a valid account.');
            return $response->withRedirect($this->router->pathFor('auth.signin'));
          }
          $this->flash->addMessage('info', 'login successfull.');
          return $response->withRedirect($this->router->pathFor('home'));

        }
        else {
          $this->flash->addMessage('info', '42 didnt accept the API.');
          return $response->withRedirect($this->router->pathFor('auth.signin'));
        }
        // end session since this is registration, 42 tokens expire quick - 2hr -  anyway.
      }
      return $response->withRedirect($this->router->pathFor('home'));
    }
    else
    {
      // if we have no access token we need to create one, per 42 endpoin requirements.
      // send the api request to 42, wi;; return GET code and state, state must match session otherwise csf
      $_SESSION['state'] = hash('whirlpool', random_int(100, 999));
      $auth_url = 'https://api.intra.42.fr/oauth/authorize?' .
      'client_id=9697f9df46513461e6ea0d6966e1298ded21bb4d58b3379d053d4cbff1e696f8' .
      '&redirect_uri=' . 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/hypertube/home/oauth/42/signin' .
      '&response_type=code&scope=public' .
      '&state=' . $_SESSION['state'];
      // redirect the users to 42 oauth, it will redirect them back here.
      return $response->withRedirect($auth_url);
    }
    return $response->withRedirect($this->router->pathFor('auth.signup'));
  }

  public function getOauth42SignUp($request, $response)
  {
    // cant check results of functions if set
    $code = $request->getParam('code');
    $state = $request->getParam('state');
    if (isset($code) && isset($state))
    {
      // api request worked, check state then send post to 42 for auth code.
      if ($state === $_SESSION['state'])
      {
        // use token from code for auth_token
        $url = 'https://api.intra.42.fr/oauth/token';
        $post = 'grant_type=authorization_code' .
        '&client_id=9697f9df46513461e6ea0d6966e1298ded21bb4d58b3379d053d4cbff1e696f8' .
        '&client_secret=81ace918b83a8e304ddbd65083a4f5d68ce798c80f83551bc0850bdf58b90721' .
        '&code=' . $code .
        '&redirect_uri=' . 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/hypertube/home/oauth/42/signup' .
        '&state=' . $_SESSION['state'];
        // send post using curl
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $api_response = curl_exec( $ch );
        curl_close($ch);
        $api_response = json_decode($api_response, true);
        // error, try again.
        if (isset($api_response['error'])) {
          $this->flash->addMessage('info', 'oauth failed, check client id etc.');
          return $response->withRedirect($this->router->pathFor('oauth.signup'));
        }
        else
          $_SESSION['42_access_token'] = $api_response['access_token'];
      }
      return $response->withRedirect($this->router->pathFor('oauth.42.signup'));
    }

    // use access token
    if (isset($_SESSION['42_access_token']) && $_SESSION['42_access_token'])
    {
      // 42 API responded with a token, so now get info bout user and register in db
      $url = 'https://api.intra.42.fr/v2/me';
      $authorization = 'Authorization: Bearer ' . $_SESSION['42_access_token'];
      $ch = curl_init( $url );
      curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
      curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt( $ch, CURLOPT_HEADER, 0);
      curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
      $api_response = curl_exec( $ch );
      curl_close($ch);
      $api_response = json_decode($api_response, true);
      if (isset($api_response['error'])) {
        unset ($_SESSION['42_access_token']);
        $this->flash->addMessage('error', 'Unable to communicate with 42.');
        return $response->withRedirect($this->router->pathFor('auth.signup'));
      }
      else {
        // retrieved info successfully, check if account exists.
        if (isset($api_response)) {

          $UserList = $this->container->profile->getUser($api_response['login']);
          $UserList2 = $this->container->profile->getUserByEmail($api_response['email']);

          if (empty($UserList) && empty($UserList2))
          {
            $user = User::create([
              'name' => $api_response['login'],
              'email' => $api_response['email'],
              'first_name' => $api_response['first_name'],
              'last_name' => $api_response['last_name'],
              'password' => 'N/A',
              'profilePicture' => 'joker-profile.png',
            ]);
            $this->flash->addMessage('info', 'account created successfully');
          }
          else {
            $this->flash->addMessage('error', 'This account exists already.');
            unset($_SESSION['42_access_token']);
            return $response->withRedirect($this->router->pathFor('auth.signup'));
          }

        }
        // end session since this is registration, 42 tokens expire quick - 2hr -  anyway.
      }
      unset($_SESSION['42_access_token']);
      return $response->withRedirect($this->router->pathFor('home'));
    }
    else
    {
      // if we have no access token we need to create one, per 42 endpoin requirements.
      // send the api request to 42, wi;; return GET code and state, state must match session otherwise csf
      $_SESSION['state'] = hash('whirlpool', random_int(100, 999));
      $auth_url = 'https://api.intra.42.fr/oauth/authorize?' .
      'client_id=9697f9df46513461e6ea0d6966e1298ded21bb4d58b3379d053d4cbff1e696f8' .
      '&redirect_uri=' . 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/hypertube/home/oauth/42/signup' .
      '&response_type=code&scope=public' .
      '&state=' . $_SESSION['state'];
      // redirect the users to 42 oauth, it will redirect them back here.
      return $response->withRedirect($auth_url);
    }
    return $response->withRedirect($this->router->pathFor('auth.signup'));
  }

  public function getSignOut($request, $response)
  {
    $this->auth->logout();
    return $response->withRedirect($this->router->pathFor('home'));
  }

  public function getSignIn($request, $response)
  {
    return $this->view->render($response, 'render/signin.twig');
  }

  public function postSignIn($request, $response)
  {
    // users can log in with email or username
    if (!empty($request->getParam('auth')))
    {
      $var = strstr($request->getParam('auth'), '@', true);
      if (!($var === false))
      {
        $validation = $this->validator->validate($request, [
          'auth' => v::noWhitespace()->notEmpty()->email(),
          'password' => v::noWhitespace()->notEmpty(),
        ]);
      }
      else
      {
        $validation = $this->validator->validate($request, [
          'auth' => v::noWhitespace()->notEmpty(),
          'password' => v::noWhitespace()->notEmpty(),
        ]);
      }

      if ($validation->failed()) {
        return $response->withRedirect($this->router->pathFor('auth.signin'));
      }

      if (!($var === false))
      {
        $auth = $this->auth->attempt(
          $request->getParam('auth'),
          $request->getParam('password'),
          0
        );
      }
      else
      {
        $auth = $this->auth->attempt(
          $request->getParam('auth'),
          $request->getParam('password'),
          1
        );
      }

      if (!$auth) {
        $this->flash->addMessage('error', 'You entered invalid details.');
        return $response->withRedirect($this->router->pathFor('auth.signin'));
      }
      $this->flash->addMessage('info', 'login successfull.');
      return $response->withRedirect($this->router->pathFor('home'));
    }
    return $response->withRedirect($this->router->pathFor('auth.signin'));

  }

  public function getSignUp($request, $response)
  {
    return $this->view->render($response, 'render/signup.twig');
  }

  public function postSignUp($request, $response)
  {
    $validation = $this->validator->validate($request, [
      'email' => v::noWhitespace()->notEmpty()->email()->emailAvailable(),
      'name' => v::notEmpty()->alpha()->usernameAvailable(),
      'password' => v::noWhitespace()->notEmpty()->passwordStrength(),
      'firstname' => v::noWhitespace()->notEmpty(),
      'lastname' => v::noWhitespace()->notEmpty(),
    ]);

    if ($validation->failed()) {
      return $response->withRedirect($this->router->pathFor('auth.signup'));
    }

    $user = User::create([
        'name' => $request->getParam('name'),
        'email' => $request->getParam('email'),
        'first_name' => $request->getParam('firstname'),
        'last_name' => $request->getParam('lastname'),
        'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
        'profilePicture' => 'joker-profile.png',
    ]);

    $this->flash->addMessage('info', 'You have signed up.');
    return $response->withRedirect($this->router->pathFor('home'));
  }
}
