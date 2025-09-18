<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\Pages;
use App\Controllers\Books;
/**
 * @var RouteCollection $routes
 */

// Route root: arahkan ke Home::index (yang redirect ke /books)
$routes->get('/', 'Home::index');

// Books CRUD routes (definisikan terlebih dahulu sebelum catch-all)
$routes->get('books', [Books::class, 'index']);            // List semua buku
$routes->get('books/new', [Books::class, 'new']);          // Form tambah buku
$routes->post('books', [Books::class, 'create']);          // Simpan buku baru (store)
$routes->get('books/(:num)/edit', [Books::class, 'edit/$1']);   // Form edit buku
$routes->post('books/(:num)/update', [Books::class, 'update/$1']); // Update buku
$routes->post('books/(:num)/delete', [Books::class, 'delete/$1']); // Hapus buku
$routes->get('books/(:num)', [Books::class, 'show/$1']);   // Detail buku

// Static pages (catch-all untuk halaman statis)
$routes->get('home', [Pages::class, 'view']);
$routes->get('(:segment)', [Pages::class, 'view']);
