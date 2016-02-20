<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

$routesConfig = include('routes.config.php');
$servicesConfig = include('services.config.php');
$controllersConfig = include('controllers.config.php');
$doctrineConfig = include('doctrine.config.php');

return array(
    'router' => $routesConfig,
    'service_manager' => $servicesConfig,
    'controllers' => $controllersConfig,
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
	'doctrine' => $doctrineConfig,
);
