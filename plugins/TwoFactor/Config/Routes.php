<?php

namespace Config;

$routes = Services::routes();

$routes->get('twofactor', 'TwoFactor::index', ['namespace' => 'TwoFactor\Controllers']);
$routes->get('twofactor/(:any)', 'TwoFactor::$1', ['namespace' => 'TwoFactor\Controllers']);
$routes->add('twofactor/(:any)', 'TwoFactor::$1', ['namespace' => 'TwoFactor\Controllers']);

$routes->get('twofactor_settings', 'TwoFactor_settings::index', ['namespace' => 'TwoFactor\Controllers']);
$routes->get('twofactor_settings/(:any)', 'TwoFactor_settings::$1', ['namespace' => 'TwoFactor\Controllers']);
$routes->post('twofactor_settings/(:any)', 'TwoFactor_settings::$1', ['namespace' => 'TwoFactor\Controllers']);

$routes->get('twofactor_updates', 'TwoFactor_Updates::index', ['namespace' => 'TwoFactor\Controllers']);
$routes->get('twofactor_updates/(:any)', 'TwoFactor_Updates::$1', ['namespace' => 'TwoFactor\Controllers']);
