<?php
namespace App\Controllers;

use App\Models\User;
use \Slim\Views\Twig as View;

class ExploreUsersController extends Controller
{
  public  function  getExploreSearch($request, $response)
  {
    if (!empty($request->getParam('search_users')))
    {
      $UserList = $this->auth->user()->getUser($request->getParam('search_users'));
      if (!empty($UserList))
      {
        $_SESSION['UserList'] = $UserList;
        $this->container->view->getEnvironment()->addGlobal('UserList', $_SESSION['UserList']);
      }
      else
        $_SESSION['UserList'] = NULL;
    }
    else
      return $response->withRedirect($this->router->pathFor('profile.users'));
    return $this->view->render($response, 'render/explore.twig');
  }
  public  function  getExplore($request, $response)
  {
    $UserList = $this->auth->user()->getUsers();
    $_SESSION['UserList'] = $UserList;
    $this->container->view->getEnvironment()->addGlobal('UserList', $_SESSION['UserList']);
    return $this->view->render($response, 'render/explore.twig');
  }
}
