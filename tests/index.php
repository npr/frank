<?php

require_once(__DIR__ . '/../frank.lib.php');
require_once(__DIR__ . '/TestController.class.php');
require_once(__DIR__ . '/FrankTestMiddleware.class.php');
require_once(__DIR__ . '/FrankTestScopedMiddleware.class.php');

// create the new router instance
$router = new Frank_Router();

// define the hook for FrankTestMiddleware
$router->attach('FrankTestMiddleware');

//create documentation endpoint at: '/docs'
$router->attach('FrankAutoDocumentator', '/testapidocs');


// Enable the middleware pluginit
$router->attach('MethodOverride');

// Enable the Cross-Origin Resource Sharing (CORS) for the domain:
$router
  ->attach('FrankCORS', '*')
  ->restrict('preroute', '*', '/users');



// The  hook for FrankTestScopedMiddleware
// The URL path you want to restrict FrankTestScopedMiddleware execution to.
$router
  ->attach('FrankTestScopedMiddleware')
  ->restrict('prerender', '*', '/foo')
  ->restrict('prerender', array('put'), '/foo/bar');

//Add the path for the router, 'users', get rule
$router->addRoute(array(
  'path' => '/users',
  'get'  => array('TestController', 'getTestJsonResponse'),
));
 
//Add the path for the router, 'users', get and post rule 
$router->addRoute(array(
  'path'     => '/users/{id}',
  'handlers' => array(
    'id'       => Frank_Constants::PATTERN_DIGIT,
  ),
  'get'      => array('TestController', 'getTestJsonResponse'),
  'post'     => array('TestController', 'getTestJsonResponse'),
  'patch'    => array('TestController', 'getTestJsonResponse'),
));

//Add the path for the router, '/v2/times/{dt}/episodes', get  rule 
$router->addRoute(array(
  'path'     => '/v2/times/{dt}/episodes',
  'get'      => array('TestController', 'getTestJsonResponse'),
));

//Add the path for the router, '/tags/{id}', get  rule 
$router->addRoute(array(
  'path'     => '/tags/{id}',
  'handlers' => array(
    'id'       => Frank_Constants::PATTERN_ALPHA,
  ),
  'get'      => array('TestController', 'getTestJsonResponse'),
));

//Add the path for the router, '/users/{user_id}/books/{book_id}, get and post rule 
$router->addRoute(array(
  'path'     => '/users/{user_id}/books/{book_id}',
  'handlers' => array(
    'user_id'  => Frank_Constants::PATTERN_NUM,
    'book_id'  => Frank_Constants::PATTERN_ALPHA,
  ),
  'get'      => array('TestController', 'getTestJsonResponse'),
));

//Add the path for the router, '/query_var_test', get rule 
$router->addRoute(array(
  'path'     => '/query_var_test',
  'get'      => array('TestController', 'getQueryVarTestJsonResponse'),
));


// Call the router for a valid setup
try {
  $router->route();
} catch (Frank_InvalidPathException $ex) {
  header('Content-Type: application/json;', true, 404);
  die(json_encode(array('error' => 'not found')));
}
