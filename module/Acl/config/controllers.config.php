<?php
return array(
	'invokables' => array(
	),
	'factories' => array(
		'Acl\Controller\Index' => function($serviceLocator) {
			$sm = $serviceLocator->getServiceLocator();

			$authenticationService = $sm->get('Acl\Authentication\Service');

			$controller = new \Acl\Controller\IndexController();
			$controller
				->setAuthenticationService($authenticationService);

			return $controller;
		},

	),
);