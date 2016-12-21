<?php
namespace App\Controllers;

use App\Models\User;
use \Slim\Views\Twig as View;

class StreamController extends Controller
{
  // jquery post
  function addMovieToList($movie, $name)
  {
    // echo $movie . ':' . $name . ':' . $this->auth->user()->id;die();
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
    }
    return $this->view->render($response, 'render/stream-page.twig');
  }
}
