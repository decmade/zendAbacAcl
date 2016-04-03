<?php
/*
 * get the key for the route parameter that the
 * Acl\Model\UserAttributeEvaluatorListener is looking for
 */
$aclDqlKey = \Acl\Model\Authorization\UserAttributeEvaluatorListener::ACCESS_DQL_PARAM_NAME;

return array(
	'routes' => array(
		'acl' => array(
			'type' => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/acl',
				'defaults' => array(
					'controller' => 'Acl\Controller\Index',
					'action'     => 'index',
					$aclDqlKey =>  array(
						array(
							"a.name = 'admin' AND a.value = '1'",
							"a.name = 'sitecode' AND a.value LIKE '74%'",
						),
						array(
							"a.name = 'developer' AND a.value = '1'",
						),
					),
				),
			),
			'may_terminate' => true,
			'child_routes' => array(
				'login' => array(
					'type' => 'Zend\Mvc\Router\Http\Segment',
					'options' => array(
						'route' => '/login',
						'defaults' => array(
							'controller' => 'Acl\Controller\Index',
							'action' => 'login',
							$aclDqlKey => null,
						),
					),
				),
				'logout' => array(
				'type' => 'Zend\Mvc\Router\Http\Segment',
				'options' => array(
					'route' => '/logout',
					'defaults' => array(
						'controller' => 'Acl\Controller\Index',
						'action' => 'logout',
						$aclDqlKey => null,
					),
				),
				),
			),
		),
	),
);