<?php
return array(
	'driver' => array(
		'acl_driver' => array(
			'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
			'cache' => 'array',
			'paths' => array(
				implode(DIRECTORY_SEPARATOR, array(
					__DIR__,
					'..',
					'src',
					'Acl',
					'Entity',
				)),
			),
		),
		'orm_default' => array(
			'drivers' => array(
				'Acl\Entity' => 'acl_driver',
			),
		),
	),
);