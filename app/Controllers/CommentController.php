<?php

namespace App\Controllers;
// have to link the users to movies they watched and each novie to a comments table
class CommentController extends Controller
{
  public function addComment($request, $response)
  {
    return $this->container->view->render($response, 'home.twig');
  }
}
