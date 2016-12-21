<?php

namespace App\Controllers;

use App\Models\User;
use \Slim\Views\Twig as View;

class HomeController extends Controller
{
    protected $page_number = 1;

    public  function  getPageNumber($request)
    {
      return $page_number = 1;
    }

    public  function    getSearchMovies($request)
    {
        if (!empty($request->getParam('search')))
        {
          $movie = $this->container->MovieController->searchMovie($request->getParam('search'));
        }
        return $movie;
    }

    public  function    getDefaultMovies()
    {
      return ($this->container->MovieController->getDefaultMovies());
    }

    public  function    index($request, $response)
    {
        if (!empty($request->getParam('search')))
            $_SESSION['movies'] = $this->getSearchMovies($request);
        else
            $_SESSION['movies'] = $this->getDefaultMovies();
        if (isset($_SESSION['user']))
          $_SESSION['watched'] = $this->container->profile_movies->getWatchedMovies($this->auth->user()->id);
        if (isset($_SESSION['watched']))
          $this->container->view->getEnvironment()->addGlobal('watched', $_SESSION['watched']);
        $this->container->view->getEnvironment()->addGlobal('movies', $_SESSION['movies']);
        $this->container->view->getEnvironment()->addGlobal('page', $this->page_number);
        return $this->view->render($response, 'render/home.twig');
    }
}
