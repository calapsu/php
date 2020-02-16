
<?php


require __DIR__.'/vendor/autoload.php';

use App\Commands\HelloWorldCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new \App\Commands\HelloWorldCommand());
$application->run();