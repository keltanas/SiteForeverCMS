<?php
return array(
    'logger'    => 'auto',
    //    'logger'    => 'html',
    //    'logger'    => 'file',

    'sitename'  => 'SiteForeverCMS',
    'siteurl'   => 'http://'.$_SERVER['HTTP_HOST'],
    'admin'     => 'admin@ermin.ru',

    'static_dir' => realpath(__DIR__ . '/../../static'),

    'url'       => array(
        'rewrite'   => true,
    ),

    'cache' => array(
        'type' => 'apc',
        'livecycle' => 600,
    ),

    //    'editor' => 'tinymce',
    'editor' => 'ckeditor',
    //    'editor' => 'elrte',

    'language'  => 'ru',

    // база данных
    'db' => array(
        'login'     => 'root',
        'password'  => '',
        'host'      => 'localhost',
        'database'  => 'siteforever',
        'migration' => false,
    ),

    // тема
    'template' => array(
        'theme'     => 'basic', // тема сайта
    ),

    'pager_template' => 'pager_twbt',

    'mailer_transport' => 'sendmail',

    'modules' => require_once __DIR__ . '/../modules.php',

);
