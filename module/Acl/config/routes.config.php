<?php
$aclAccessDql = array(
	array(
		"a.name = 'admin' AND a.value = '1'",
		"a.name = 'sitecode' AND a.value LIKE '74%'",
	),
	array(
		"a.name = 'developer' AND a.value = '1'",
	),
);

return array(
	'routes' => array(
		'acl' => array(
			'type' => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/acl',
				'defaults' => array(
					'controller' => 'Acl\Controller\Index',
					'action'     => 'index',
					'accessDqlConfig' => $aclAccessDql,
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