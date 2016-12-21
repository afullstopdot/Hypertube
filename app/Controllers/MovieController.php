<?php
namespace App\Controllers;

use App\Models\User;
use \Slim\Views\Twig as View;

class MovieController extends Controller
{
  protected $yts_api = 'https://yts.ag/api/v2/list_movies.json';
  protected $last_search;
  protected $page = 1;

  public  function  pager($request, $response)
  {
      // regex to replace last searched uri value with either next or prev
  }

  public  function  searchMovie($title)
  {
    $this->last_search = $this->yts_api . '?sort_by=title&query_term=' . urlencode ($title) . '&page=' . $this->page;
    $movie = file_get_contents($this->last_search);
    $movie = json_decode($movie, true);
    if ($movie['status'] === 'ok')
    {
      $movie = $movie['data']['movies'];
      $this->page = intval($response['data']['page_number']);
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
    $this->last_search = $this->yts_api . '?sort_by=year&limit=48&page=' . $this->page;
    $file = file_get_contents($this->last_search, true);
    $response = json_decode($file, true);
    if ($response['status'] === 'ok')
    {
      $response = $response['data']['movies'];
      $this->page = intval($response['data']['page_number']);
      return ($response);
    }
    else
      $this->flash->addMessage('error', 'YTS server error.');
  }
}
