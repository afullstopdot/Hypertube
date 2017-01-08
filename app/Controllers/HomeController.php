<?php

namespace App\Controllers;

use App\Models\User;
use \Slim\Views\Twig as View;

class HomeController extends Controller
{
    protected $page_number = 1;

    public  function  getPager($request, $response)
    {
      if (!isset($_SESSION['page_number']))
        $_SESSION['page_number'] = 1;
      if (!empty($request->getParam('pager')))
      {
        $page = $request->getParam('pager');
        if ($page === 'next')
          return (json_encode(array($this->container->MovieController->getNextPage(), $_SESSION['watched'], 'page' => $_SESSION['page_number'])));
        if ($page === 'previous')
          return (json_encode(array($this->container->MovieController->getPreviousPage(), $_SESSION['watched'], 'page' => $_SESSION['page_number'])));
      }
      return json_encode(array($this->getDefaultMovies(), $_SESSION['watched'], 'page' => $_SESSION['page_number']));
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

    public   function   time_diff($b, $a) {
      // get date diff in days
      $date1 = date_create($a);
      $date2 = date_create($b);

      $diff = date_diff($date1, $date2);
      return ($diff->format("%R%a days"));
    }

    public  function    removeMovies($list)
    {
      // check difference in days
      foreach ($list as $value) {
        $days  = intval(trim($this->time_diff($value['updated_at'], $value['created_at']), '+-'));
        if ($days > 30) {
          // hasnt been watched for a month, remove
          $path = realpath($value['full_path'] . '/' . $value['path']);
          if (is_writable($path)) {
            // is writable delete
            if (unlink($path) == true) //remove in db aswell
              $this->container->downloads->removeInactive($value['id']);
            else
              echo 'Unlink failed to delete movie, try again.';
          }
        }
      }
    }

    public  function    index($request, $response)
    {
      // remove movies that havent been watched in over a month
      $inactive = $this->container->downloads->getInactive();
      if (!empty($inactive))
        $this->removeMovies($inactive);
      // reset default valu es for default movie list params
      $_SESSION['genre'] = 'all';
      $_SESSION['imdb'] = 'all';
      $_SESSION['sort-by'] = 'date_added';
      $_SESSION['order-by'] = 'desc';
      // check if sort and filter vars are set, then update the request uri
      if (!empty($request->getParam('sort-by')))
        $_SESSION['sort-by'] = $request->getParam('sort-by');
      if (!empty($request->getParam('genre')))
        $_SESSION['genre'] = $request->getParam('genre');
      if (!empty($request->getParam('imdb')))
        $_SESSION['imdb'] = $request->getParam('imdb');
      if (!empty($request->getParam('order-by')))
        $_SESSION['order-by'] = $request->getParam('order-by');
      // affect new movie list now
      if (!empty($request->getParam('search')))
      {
        $_SESSION['sort-by'] = 'title';
        $_SESSION['movies'] = $this->getSearchMovies($request);
      }
      else
        $_SESSION['movies'] = $this->getDefaultMovies();
      if (isset($_SESSION['user']))
        $_SESSION['watched'] = $this->container->profile_movies->getWatchedMovies($this->auth->user()->id);
      if (isset($_SESSION['watched']))
        $this->container->view->getEnvironment()->addGlobal('watched', $_SESSION['watched']);
      $_SESSION['old_movies'] = $_SESSION['movies'];
      $this->container->view->getEnvironment()->addGlobal('movies', $_SESSION['movies']);
      $this->container->view->getEnvironment()->addGlobal('page', $_SESSION['page_number']);
      return $this->view->render($response, 'render/home.twig');
    }
}
