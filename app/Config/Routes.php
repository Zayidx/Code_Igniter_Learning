<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\Pages;
/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
// Static pages
$routes->get('home', [Pages::class, 'view']);
$routes->get('(:segment)', [Pages::class, 'view']);
