<?php
if (is_file(__DIR__.'/default/settings-example.php')) {
    return include (__DIR__.'/default/settings-example.php');
}

$settings = array(
    'db' => array(
        'driver' => 'mysql',
        'host' => 'localhost',
        'database' => '',
        'user' => '',
        'password' => '',
        'register_queries' => true
    ),

    'cache' => array(
        'expire' => 0,
        'interface' => 'apc',
        'exception' => true
    ),

    'languages' => array('es', 'en', 'gl'),

    'language' => 'es',

    'tables' => array(
        'categories' => array(
            'code' => 'id_text',
            'name' => 'varchar',
            'enabled' => 'boolean'
        ),

        'comments' => array(
            'code' => 'id_text',
            'text' => 'text',
            'date' => 'datetime',
            'enabled' => 'boolean'
        ),

        'posts' => array(
            'code' => 'id_text',
            'title' => 'varchar',
            'text' => 'text',
            'date' => 'datetime',
            'enabled' => 'boolean'
        ),

        'users' => array(
            'code' => 'id_text',
            'name' => 'varchar',
            'email' => array(
                'format' => 'email',
                'required' => true,
                'unique' => true
            ),
            'enabled' => 'boolean'
        )
    ),

    'relations' => array(
        array(
            'tables' => 'categories posts',
            'mode' => 'x x'
        ),

        array(
            'tables' => 'comments posts',
            'mode' => 'x 1',
            'dependent' => true
        ),

        array(
            'tables' => 'comments users',
            'mode' => 'x 1',
            'dependent' => true
        ),

        array(
            'tables' => 'posts users',
            'mode' => 'x 1',
            'dependent' => true
        )
    )
);
