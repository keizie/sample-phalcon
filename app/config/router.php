<?php

$router = $di->getRouter();

// Define your routes here
// TODO: use phalcon multi module
$router->add('/api/:controller/:action/:params', [
    'namespace'  => 'Sample\Controller\Api',
    'controller' => 1,
    'action'     => 2,
    'params'     => 3,
]);
$router->add('/api/:controller/:action', [
    'namespace'  => 'Sample\Controller\Api',
    'controller' => 1,
    'action'     => 2,
]);
$router->add('/api/:controller/:int', [
    'namespace'  => 'Sample\Controller\Api',
    'controller' => 1,
    'action'     => 'read',
    'params'     => 2,
]);
$router->add('/api/:controller', [
    'namespace'  => 'Sample\Controller\Api',
    'controller' => 1,
    'action'     => 'index',
]);
$router->addPost('/api/:controller', [
    'namespace'  => 'Sample\Controller\Api',
    'controller' => 1,
    'action'     => 'create',
]);

$router->handle($_SERVER['REQUEST_URI']);
