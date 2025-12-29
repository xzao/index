<?php

    # config
    define( 'DEBUG',        get_debug()                            );
    define( 'DESCRIPTION',  get_description()                      );
    define( 'ETC',          realpath('/etc/index')                 );
    define( 'HOST',         explode(':', $_SERVER['HTTP_HOST'])[0] );
    define( 'IGNORE',       array('.', '..', '.gitignore')         );
    define( 'PROTOCOL',     get_protocol()                         );
    define( 'ROOT',         realpath(__DIR__ .  '/..')             );
    define( 'SRC',          realpath(__DIR__)                      );
    define( 'TITLE',        get_title()                            );
    define( 'WWW',          realpath(__DIR__ . '/public')          );

    # config default image
    define( 'DEFAULT_IMAGE', array(
        'name' => 'default',
        'path' => 'vendor/selfhst-icons/svg/ubuntu.svg',
        'type' => 'svg'
    ));

    # config default site
    define( 'DEFAULT_SITE', array(
        'classes'     => array(),
        'description' => '',
        'logo'        => 'default.png',
        'tagline'     => ''
    ));

    # config default styles
    define( 'DEFAULT_STYLES', array(
        "background-color" => "#1C2833",
        "color" => "#F5F5F5"
    ));
