<?php

namespace App\Controllers;

use App\Models\User;
use \Slim\Views\Twig as View;

class HomeController extends Controller
{
    protected  $yts_movielist_api = 'https://yts.ag/api/v2/list_movies.json';
    protected  $yts_request;
    protected  $page = 1;

    public  function    setPageNumber($request)
    {
      if (!empty($request->getParam('next_page')))
      {
        $this->page = $this->page + 1;
        $this->yts_request = preg_replace('page=[0-9+]', $this->page, $this->yts_request);
        $movie = file_get_contents($this->yts_request);
        $movie = json_decode($movie, true);
        if ($movie['status'] !== 'ok')
        {
          $this->flash->addMessage('error', preg_replace('page=[0-9+]', $this->page, $this->yts_request));
          $this->page = $this->page - 1;
        }
        $response = $response['data']['movies'];
      }
    }

    public  function    searchMovies($request)
    {
        if (!empty($request->getParam('search')))
        {
          $this->yts_request = $this->yts_movielist_api . '?sort=title&query_term=' . urlencode ($request->getParam('search')) . '&page=' . $this->page;
          $movie = file_get_contents($this->yts_request);
          $movie = json_decode($movie, true);
          if ($movie['status'] === 'ok')
          {
              $movie = $movie['data']['movies'];
              $this->page = $response['data']['page_number'];
          }
          else
            $this->flash->addMessage('error', 'Unsuccessful request.');
        }
        return $movie;
    }

    public  function    getDefaultMovies()
    {
        $this->yts_request = 'https://yts.ag/api/v2/list_movies.json?sort_by=seeds&limit=48&page=' . $this->page;
        $file = file_get_contents($this->yts_request, true);
        $response = json_decode($file, true);
        if ($response['status'] === 'ok')
        {
          $response = $response['data']['movies'];
          $this->page = $response['data']['page_number'];
          return ($response);
        }
        return NULL;
    }

    public  function    index($request, $response)
    {
        if (!empty($request->getParam('search')))
            $_SESSION['movies'] = $this->searchMovies($request);
        else
            $_SESSION['movies'] = $this->getDefaultMovies();
        $this->container->view->getEnvironment()->addGlobal('movies', $_SESSION['movies']);
        $this->container->view->getEnvironment()->addGlobal('page', $this->page);
        return $this->view->render($response, 'render/home.twig');
    }
}
