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
					'attributePatternSetConfig' => array(
						/*
						 * each element is evaluated with an OR operator
						 * a nested array is evaluated as single value like
						 * an AND would behanve
						*/
						array(
							'name' => 'admin',
							'value' => '1',
						),
						array(
							array(
								array(
									'name' => 'jobtitle',
									'value' => 'managaer',
								),
								array(
									'name' => 'jobtitle',
									'value' => 'supervisor',
								),
							),
							array(
								'name' => 'location',
								'value' => 'IT',
							),
						),
					),
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