<?php
require './vendor/autoload.php';

use Api\Handler\Product;
use Phalcon\Mvc\Micro;
use Phalcon\Loader;
use Phalcon\Di\FactoryDefault;
use Phalcon\Events\Manager as EventsManager;

$loader = new Loader();
$loader->registerNamespaces(
    [
        'Api\Components' => './components',
        'Api\Handler' => './handler'
    ]
);

$loader->register();


$container = new FactoryDefault();
$app =  new Micro($container);

$container->set(
    'mongo',
    function () {
        $mongo = new \MongoDB\Client(
            "mongodb://mongo",
            array(
                "username" => 'root',
                "password" => "password123"
            )
        );
        return $mongo->store;
    },
    true
);

$prod = new Product();

$eventsManager = new EventsManager();
$eventsManager->attach(
    'micro',
    new Api\Components\GenerateToken()
);
$app->before(
    new Api\Components\GenerateToken()
);


$app->get(
    '/api/products/search/{keyword}',
    [
        $prod,
        'search'
    ]
);

$app->get(
    '/api/products/get/{per_page}/{page}',
    [
        $prod,
        'get'
    ]
);

$app->get(
    '/api/generateApiToken',
    [
        $prod,
        'generateToken'
    ]
);

$app->setEventsManager($eventsManager);
$app->handle(
    $_SERVER["REQUEST_URI"]
);
