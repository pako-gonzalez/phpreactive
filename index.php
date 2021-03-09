<?php

use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use React\Http\Response;
use React\Http\Server;
use React\MySQL\Factory;

require __DIR__.'/vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

$factory = new Factory($loop);
$db = $factory->createLazyConnection('root:root@mysqlreactive/users');

$listUsers = function () use ($db) {
    return $db->query('SELECT id, name, email FROM users ORDER BY id')
               ->then(function (\React\MySQL\QueryResult $queryResult) {
                return new React\Http\Message\Response(
                    200,
                    array('Content-Type' => 'application/json'),
                    json_encode($queryResult->resultRows)
                );
            });
};

$routes = new RouteCollector(new Std(), new GroupCountBased());
$routes->get('/users', $listUsers);

$server = new Server($loop, new \App\Router($routes));

$socket = new React\Socket\Server('0.0.0.0:8000', $loop);
$server->listen($socket);

echo "Server running at ".str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;;

$loop->run();

