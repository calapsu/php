<?php

ini_set('display_errors', 1);  //inicia las variables de php errores
ini_set('display_starup_error',1);
error_reporting(E_ALL); //constante php reporte de todos los errores

require_once '../vendor/autoload.php';

//para inicialisar las secciones 

session_start();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();




use Illuminate\Database\Capsule\Manager as Capsule;
use Aura\Router\RouterContainer;


$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => getenv('DB_DRIVER'),
    'host'      => getenv('DB_HOST'),
    'database'  => getenv('DB_NAME'),
    'username'  => getenv('DB_USER'),
    'password'  => getenv('DB_PASS'),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();
// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);
$routerContainer = new RouterContainer();
$map = $routerContainer->getMap();
$map->get('index', '/php/' , [
    'controller' => 'App\Controllers\IndexController',
    'action' => 'indexAction'
]);
$map->get('addJobs', '/php/jobs/add' , [
    'controller' => 'App\Controllers\JobsController',
    'action' => 'getAddJobAction',
    'auth' => true
]);
$map->post('saveJobs', '/php/jobs/add' , [
    'controller' => 'App\Controllers\JobsController',
    'action' => 'getAddJobAction'
]);

$map->get('addUser', '/php/users/add', [
    'controller' => 'App\Controllers\UsersController',
    'action' => 'getAddUser'
]);

$map->post('saveUser', '/php/users/save', [
    'controller' => 'App\Controllers\UsersController',
    'action' => 'postSaveUser'
]);

$map->get('loginForm', '/php/login', [
    'controller' => 'App\Controllers\AuthController',
    'action' => 'getLogin'
]);

$map->get('logout', '/php/logout', [
    'controller' => 'App\Controllers\AuthController',
    'action' => 'getLogout'
]);

$map->post('auth', '/php/auth', [
    'controller' => 'App\Controllers\AuthController',
    'action' => 'postLogin'
]);

$map->get('admin', '/php/admin', [
    'controller' => 'App\Controllers\AdminController',
    'action' => 'getIndex',
    'auth' => true
]);

$matcher = $routerContainer->getMatcher();
$route = $matcher->match($request);

  
function printElement($job) {
    // if($job->visible == false) {
    //   return;
    // }
  
    echo '<li class="work-position">';
    echo '<h5>' . $job->title . '</h5>';
    echo '<p>' . $job->description . '</p>';
    echo '<p>' . $job->getDurationAsString() . '</p>';
    echo '<strong>Achievements:</strong>';
    echo '<ul>';
    echo '<li>Lorem ipsum dolor sit amet, 80% consectetuer adipiscing elit.</li>';
    echo '<li>Lorem ipsum dolor sit amet, 80% consectetuer adipiscing elit.</li>';
    echo '<li>Lorem ipsum dolor sit amet, 80% consectetuer adipiscing elit.</li>';
    echo '</ul>';
    echo '</li>';
}

if(!$route) {
    echo 'No route';
}else {
   $handlerData = $route->handler;
   $controllerName = $handlerData['controller'];
   $actionName = $handlerData['action'];
   $needsAuth = $handlerData['auth'] ?? false;

   $sessionUserId = $_SESSION['userId'] ?? null;

   if ($needsAuth && !$sessionUserId) {
       echo 'Protected route';
       //terminar escripts es die
       die;
   }

   $controller = new $controllerName;
   $response = $controller->$actionName($request);


   //foreach vamos a recorrer una arreglo y cada elemento lo vamos a responde 

   foreach($response->getHeaders() as $name => $values)
   {
       foreach($values as $value) {
           header(sprintf('%s: %s', $name, $value), false);
       }
   }
   //http-response_code : nos permite identifacr en tipo de coidgo que se va a enviar  repuesta
   http_response_code($response->getStatusCode());
   echo $response->getBody();
}


