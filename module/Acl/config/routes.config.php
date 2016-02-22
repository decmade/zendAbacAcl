<?php
return array(
	'routes' => array(
		'acl' => array(
			'type' => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/acl',
				'defaults' => array(
					'controller' => 'Acl\Controller\Index',
					'action'     => 'index',
				),
				),
			'may_terminate' => true,
			'child_routes' => array(
				'logout' => array(
					'type' => 'Zend\Mvc\Router\Http\Segment',
					'options' => array(
						'route' => '/logout',
						'defaults' => array(
							'controller' => 'Acl\Controller\Index',
							'action' => 'logout',
						),
					),
				),
			),
		),
	),
);