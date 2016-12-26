<?php
namespace App\Controllers;

use App\Models\User;
use \Slim\Views\Twig as View;

class StreamController extends Controller
{
  // jquery post
  function addMovieToList($movie, $name)
  {
    $this->container->profile_movies->addWatchedMovies(
      $movie,
      $name,
      $this->auth->user()->id
    );
  }

  function index($request, $response)
  {
    if (!(empty($request->getParam('movie'))) && !(empty($request->getParam('name'))))
    {
      $this->addMovieToList(
        $request->getParam('movie'),
        urldecode($request->getParam('name'))
      );
      $_SESSION['movie_details'] = $this->container->MovieController->getMovieDetails($request->getParam('movie'));
    }
    $this->container->view->getEnvironment()->addGlobal('movie_details', $_SESSION['movie_details']);
    return $this->view->render($response, 'render/stream.twig');
  }
}
