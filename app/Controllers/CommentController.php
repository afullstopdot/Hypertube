<?php

namespace App\Controllers;
// have to link the users to movies they watched and each novie to a comments table
class CommentController extends Controller
{
  public function getComments($movie_id)
  {
    return ($this->container->profile_comments->getMovieComments($movie_id));
  }
  // ajax form post, add the comment then return a json str with Updated comments.
  // eventually this must all be done via ajax, so comments are loaded without having to refresh
  public function addComment($request, $response)
  {
    if (!empty($request->getParam('request')) && !empty($request->getParam('movie')))
    {
      return json_encode($this->getComments($request->getParam('movie')));
    }

    if (!empty($request->getParam('comment')) && !empty($request->getParam('movie')))
    {
      $this->container->profile_comments->addComment(
        $request->getParam('movie'),
        $this->auth->user()->id,
        $request->getParam('comment'),
        $this->auth->user()->name,
        $this->auth->user()->profilePicture
      );

      return json_encode($this->getComments($request->getParam('movie')));
    }
    return $this->container->view->render($response, 'render/stream.twig');
  }
}
