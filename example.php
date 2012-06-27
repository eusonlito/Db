<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

echo '<pre>';

include (__DIR__.'/libs/ANS/Db/Db.php');
include (__DIR__.'/settings-example.php');

$Db = new \ANS\Db\Db($settings);

var_dump($Db->select(array(
	'table' => 'posts',
	'fields' => '*',
	'conditions' => array(
		'id >' => 10
	),
	'add_tables' => array(
		array(
			'table' => 'comments',
			'fields' => '*',
			'limit' => 5,
			'conditions' => array(
				'enabled' => 1
			)
		)
	)
)));