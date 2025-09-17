<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\Pages;
use App\Controllers\Books;
/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Books CRUD routes
$routes->get('books', [Books::class, 'index']);
$routes->get('books/new', [Books::class, 'new']);
$routes->post('books', [Books::class, 'create']);
$routes->get('books/(:num)/edit', [Books::class, 'edit/$1']);
$routes->post('books/(:num)/update', [Books::class, 'update/$1']);
$routes->post('books/(:num)/delete', [Books::class, 'delete/$1']);
$routes->get('books/(:num)', [Books::class, 'show/$1']);

// Static pages
$routes->get('home', [Pages::class, 'view']);
$routes->get('(:segment)', [Pages::class, 'view']);

