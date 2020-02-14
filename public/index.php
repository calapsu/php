<?php
require_once '../vendor/autoload.php';

//para inicialisar las secciones 

session_start();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

if (getenv('DEBUG') === 'true') {
    ini_set('display_errors', 1);  //inicia las variables de php errores
    ini_set('display_starup_error',1);
    error_reporting(E_ALL); //constante php reporte de todos los errores
}



use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use Illuminate\Database\Capsule\Manager as Capsule;
use Aura\Router\RouterContainer;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use WoohooLabs\Harmony\Harmony;
use WoohooLabs\Harmony\Middleware\DispatcherMiddleware;
use WoohooLabs\Harmony\Middleware\FastRouteMiddleware;
use WoohooLabs\Harmony\Middleware\LaminasEmitterMiddleware;
use WoohooLabs\Harmony\Middleware\HttpHandlerRunnerMiddleware;

$log = new Logger('app');
$log->pushHandler(new StreamHandler(__DIR__. '/../logs/app.log', Logger::WARNING));

$container = new DI\Container();
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
    'App\Controllers\IndexController',
    'indexAction'
]);

$map->get('indexJobs', '/php/jobs' , [
     'App\Controllers\JobsController',
    'indexAction',
    
]);

$map->get('addJobs', '/php/jobs/add' , [
    'controller' => 'App\Controllers\JobsController',
     'getAddJobAction',
    'auth' => true
]);

$map->get('deleteJobs', '/php/jobs/delete' , [
     'App\Controllers\JobsController',
     'deleteAction',
    
]);

$map->post('saveJobs', '/php/jobs/add' , [
     'App\Controllers\JobsController',
     'getAddJobAction'
]);

$map->get('addUser', '/php/users/add', [
     'App\Controllers\UsersController',
     'getAddUser'
]);

$map->post('saveUser', '/php/users/save', [
     'App\Controllers\UsersController',
    'postSaveUser'
]);

$map->get('loginForm', '/php/login', [
    'App\Controllers\AuthController',
    'getLogin'
]);

$map->get('logout', '/php/logout', [
     'App\Controllers\AuthController',
     'getLogout'
]);

$map->post('auth', '/php/auth', [
     'App\Controllers\AuthController',
    'postLogin'
]);

$map->get('admin', '/php/admin', [
     'App\Controllers\AdminController',
     'getIndex',
    'auth' => true
]);

$matcher = $routerContainer->getMatcher();
$route = $matcher->match($request);


$matcher = $routerContainer->getMatcher();
$route = $matcher->match($request);

if (!$route ) {
    echo 'No route';
} else {
    try{
        $harmony = new Harmony($request, new Response());
        $harmony
        ->addMiddleware(new HttpHandlerRunnerMiddleware(new SapiEmitter()));
    if (getenv('DEBUG') === 'true') {
        $harmony->addMiddleware(new \Franzl\Middleware\Whoops\WhoopsMiddleware());
    }
    $harmony->addMiddleware(new \App\Middlewares\AuthenticationMiddleware())
        ->addMiddleware(new Middlewares\AuraRouter($routerContainer))
        ->addMiddleware(new DispatcherMiddleware($container, 'request-handler'))
        ->run();

        //cuando algo no funciona en la pagina manejar error

    } catch (Exception $e) {
        $log->warning($e->getMessage());
        $emitter = new SapiEmitter();
        $emitter->emit(new Response\EmptyResponse(400));
    } catch (Error $e) {
        $emitter = new SapiEmitter();
        $emitter->emit(new Response\EmptyResponse(500));
    }


}

