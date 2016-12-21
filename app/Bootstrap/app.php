<?PHP

use Respect\Validation\Validator as v;

session_start();

ini_set('display_errors', true);

require __DIR__ . '/../../vendor/autoload.php';

$app = new \Slim\App([
  'settings' => [
    'displayErrorDetails' => true,
    'addContentLengthHeader' => false,
    'db' => [
      'driver' => 'mysql',
      'host' => 'localhost',
      'database' => 'db_hypertube',
      'username' => 'root',
      'password' => '0723573853',
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci',
      'prefix' => '',
    ]
  ],
]);

$container = $app->getContainer();

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function ($container) use ($capsule) {
  return $capsule;
};

$container['flash'] = function ($container) {
  return new \Slim\Flash\Messages;
};

$container['auth'] = function ($container) {
    return new \App\Controllers\Auth;
};
// incase of errors, change the name of the user model to user from profile
$container['user'] = function ($container) {
  return new App\Models\User;
};

$container['view'] = function ($container) {

  $view = new \Slim\Views\Twig(__DIR__ . '/../Views', [
      'cache' => false,
  ]);

  $view->addExtension(new \Slim\Views\TwigExtension(
    $container->router,
    $container->request->getUri()
  ));

  $view->getEnvironment()->addGlobal('auth', [
    'check' => $container->auth->check(),
    'user' => $container->auth->user(),
  ]);

  $view->getEnvironment()->addGlobal('flash', $container->flash);
  return $view;
};

$container['validator'] = function ($container) {
  return new App\Validation\Validator;
};

$container['profile'] = function ($container) {
  return new App\Models\User;
};

$container['profile_movies'] = function ($container) {
  return new App\Models\Movie;
};

$container['HomeController'] = function($container) {
    return new \App\Controllers\HomeController($container);
};

$container['AuthController'] = function($container) {
    return new \App\Controllers\AuthController($container);
};

$container['PasswordController'] = function($container) {
    return new \App\Controllers\PasswordController($container);
};

$container['ProfileController'] = function($container) {
    return new \App\Controllers\ProfileController($container);
};

$container['MovieController'] = function($container) {
    return new \App\Controllers\MovieController($container);
};

$container['StreamController'] = function($container) {
    return new \App\Controllers\StreamController($container);
};

$container['CommentController'] = function ($container) {
    return new \App\Controllers\CommentController($container);
};

$container['ExploreUsersController'] = function ($container) {
    return new \App\Controllers\ExploreUsersController($container);
};

$container['csrf'] = function ($container) {
    return new \Slim\Csrf\Guard;
};

$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\OldInputMiddleware($container));
$app->add(new \App\Middleware\CsrfViewMiddleware($container));
$app->add($container->csrf);

v::with('App\\Validation\\Rules\\');

require __DIR__ . '/./routes.php';
