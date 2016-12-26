<?php

use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

$app->get('/', 'HomeController:index')->setName('home');

$app->group('', function () {

  $this->get('/auth/signup', 'AuthController:getSignUp')->setName('auth.signup');
  $this->post('/auth/signup', 'AuthController:postSignUp');
  $this->get('/oauth/google/signup', 'AuthController:getOauthGoogleSignUp')->setName('oauth.google.signup');
  $this->get('/oauth/42/signup', 'AuthController:getOauth42SignUp')->setName('oauth.42.signup');

  $this->get('/auth/signin', 'AuthController:getSignIn')->setName('auth.signin');
  $this->post('/auth/signin', 'AuthController:postSignIn');
  $this->get('/oauth/google/signin', 'AuthController:getOauthGoogleSignIn')->setName('oauth.google.signin');
  $this->get('/oauth/42/signin', 'AuthController:getOauth42SignIn')->setName('oauth.42.signin');

  $this->get('/auth/reset', 'PasswordController:getReset');
  $this->post('/auth/reset', 'PasswordController:postReset')->setName('auth.reset');
  $this->post('/auth/reset/change', 'PasswordController:postResetChangePassword')->setName('auth.reset.change');

})->add(new GuestMiddleware($container));

$app->group('', function() {

  $this->get('/auth/signout', 'AuthController:getSignOut')->setName('auth.signout');

  $this->get('/profile', 'ProfileController:getProfile')->setName('home.profile');
  $this->post('/profile/picture/change', 'ProfileController:changeProfilePicture')->setName('profile.picture.change');
  $this->post('/profile/bio/change', 'ProfileController:changeProfileBio')->setName('profile.bio.change');
  $this->get('/profile/explore', 'ExploreUsersController:getExplore')->setName('profile.users');
  $this->get('/profile/explore/search', 'ExploreUsersController:getExploreSearch')->setName('profile.users.search');

  $this->post('/search', 'HomeController:searchMovies')->setName('home.search');
  $this->get('/search/change', 'HomeController:getPager')->setName('pager');

  $this->get('/auth/password/change', 'PasswordController:getChangePassword')->setName('auth.password.change');
  $this->post('/auth/password/change', 'PasswordController:postChangePassword');

  $this->get('/stream', 'StreamController:index')->setName('home.stream');
  $this->get('/stream/meta', 'StreamController:addMovieToList')->setName('stream.meta');
  $this->get('/stream/comment', 'CommentController:addComment')->setName('stream.comment');

})->add(new AuthMiddleware($container));
