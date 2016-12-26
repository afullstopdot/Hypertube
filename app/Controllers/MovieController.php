<?php
namespace App\Controllers;

use App\Models\User;
use \Slim\Views\Twig as View;

class MovieController extends Controller
{
  protected $yts_api = 'https://yts.ag/api/v2/list_movies.json';
  protected $last_search = NULL;
  protected $page = 1;

  private function  checkPageNumValid($url)
  {
    $movie = file_get_contents($url);
    $movie = json_decode($movie, true);
    if ($movie['status'] === 'ok')
      return (intval($movie['data']['page_number']));
    return (0);
  }

  private function  searchURI($url)
  {
      $movie = file_get_contents($url);
      $movie = json_decode($movie, true);
      if ($movie['status'] === 'ok')
        $movie = $movie['data']['movies'];
      return ($movie);
  }

  public  function  getMovieDetails($movie)
  {
    $url = 'https://yts.ag/api/v2/movie_details.json?movie_id=' . $movie . '&with_cast=true';

    $movie_details = file_get_contents($url);
    $movie_details = json_decode($movie_details, true);
    if ($movie_details['status'] === 'ok')
    {
      $omdb_url = 'http://www.omdbapi.com/?i=' . $movie_details['data']['movie']['imdb_code'] . '&plot=full&r=json';
      $omdb_response = file_get_contents($omdb_url);
      $omdb_details = json_decode($omdb_response, true);
      if ($omdb_details['Response'] === 'True')
        return ($omdb_details);
      $movie_details['data']['movie'];
    }
    return (NULL);
  }

  public  function  getNextPage()
  {
    if (isset($_SESSION['page_number']))
      $this->page = intval($_SESSION['page_number']) + 1;
    $replacement = 'page=' . $this->page;
    if (isset($_SESSION['last_search']))
      $this->last_search = preg_replace("/page=\d+/i", $replacement, $_SESSION['last_search']);
    if (($response = $this->checkPageNumValid($this->last_search)) != 0)
    {
      $_SESSION['page_number'] = intval($response);
      $this->container->view->getEnvironment()->addGlobal('page', $_SESSION['page_number']);
      $_SESSION['last_search'] = $this->last_search;
      return $this->searchURI($_SESSION['last_search']);
    }
    return ($this->getDefaultMovies());
  }

  public  function  getPreviousPage()
  {
    if (isset($_SESSION['page_number']))
    {
      if ((intval($_SESSION['page_number']) - 1) > 0)
        $this->page = intval($_SESSION['page_number']) - 1;
    }
    $replacement = 'page=' . $this->page;
    if (isset($_SESSION['last_search']))
      $this->last_search = preg_replace("/page=\d+/i", $replacement, $_SESSION['last_search']);
    if (($response = $this->checkPageNumValid($this->last_search)) != 0)
    {
      $_SESSION['page_number'] = intval($response);
      $this->container->view->getEnvironment()->addGlobal('page', $_SESSION['page_number']);
      $_SESSION['last_search'] = $this->last_search;
      return $this->searchURI($_SESSION['last_search']);
    }
    return ($this->getDefaultMovies());
  }

  public  function  searchMovie($title)
  {
    if (isset($_SESSION['page_number']))
      $_SESSION['page_number'] = intval(1);
    $this->last_search = $this->yts_api . '?sort_by=title&limit=15&query_term=' . urlencode ($title) . '&page=' . $_SESSION['page_number'];
    $_SESSION['last_search'] = $this->last_search;
    $movie = file_get_contents($this->last_search);
    $movie = json_decode($movie, true);
    if ($movie['status'] === 'ok')
    {
      $_SESSION['page_number'] = intval($movie['data']['page_number']);
      $movie = $movie['data']['movies'];
    }
    else
    {
      $this->flash->addMessage('error', 'Unsuccessful request.');
      $movie = $this->getDefaultMovies();
    }
    return ($movie);
  }

  public  function  getDefaultMovies()
  {
    $this->last_search = $this->yts_api . '?sort_by=year&limit=15&page=' . $this->page;
    $_SESSION['last_search'] = $this->last_search;
    $file = file_get_contents($this->last_search, true);
    $response = json_decode($file, true);
    if ($response['status'] === 'ok')
    {
      $_SESSION['page_number'] = intval($response['data']['page_number']);
      $response = $response['data']['movies'];
      return ($response);
    }
    else
      $this->flash->addMessage('error', 'YTS server error.');
  }
}
