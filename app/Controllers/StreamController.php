<?php
namespace App\Controllers;

use App\Models\User;
use \Slim\Views\Twig as View;

class StreamController extends Controller
{
  function getTorrent($movie, $name)
  {
      $list = $_SESSION['movies'];

      foreach ($list as $value) {
        if ($value['id'] == $movie)
        {
          $torrents = $value['torrents'];
          break;
        }
      }

      if (sizeof($torrents) > 1) {
        foreach ($torrents as $value) {
          if ($value['quality'] == '1080p')
            return ($value['url']);
        }
      }
      else {
        foreach ($torrents as $value) {
            return ($value['url']);
        }
      }
  }

  //theoretically, download the movie add to db move to folder
  function downloadMovie($movie_id)
  {
    // download torrent lol, testing purposes The Dark Knight
    if ($movie_id == '3175' || $movie_id == '6264')
    {
      $this->container->downloads->addDownloadedMovie(
        $movie_id,
        $movie_id . '.mp4',
        $_SERVER['DOCUMENT_ROOT'] . '/hypertube/home/movies/'
      );
    }
  }

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
      $this->downloadMovie($request->getParam('movie'));
      $_SESSION['movie_details'] = $this->container->MovieController->getMovieDetails($request->getParam('movie'));
      $_SESSION['movie_path'] = $this->container->downloads->getMoviePath($request->getParam('movie'));
    }
    $this->container->view->getEnvironment()->addGlobal('movie_details', $_SESSION['movie_details']);
    $this->container->view->getEnvironment()->addGlobal('movie_path', $_SESSION['movie_path']);
    return $this->view->render($response, 'render/stream.twig');
  }
}
