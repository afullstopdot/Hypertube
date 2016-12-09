<?php
namespace App\Controllers;
// collect user info, IP Address, Geolocation and Number of times IP visited
use App\Models\User;
use \Slim\Views\Twig as View;

class MetaController extends Controller
{
    public  function    getIP()
    {
      if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $return ($_SERVER['HTTP_CLIENT_IP']);
      }
      else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $return ($_SERVER['HTTP_X_FORWARDED_FOR']);
      }
      else {
        return ($_SERVER['REMOTE_ADDR']);
      }
    }

    public  function    index($request, $response)
    {
        return $this->view->render($response, 'render/home.twig');
    }
}
