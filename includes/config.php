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
        'path' => 'vendor/selfhst-icons/svg/linux-containers-lxc-light.svg',
        'type' => 'svg'
    ));

    # config default site
    define( 'DEFAULT_SITE', array(
        'classes'     => array(),
        'description' => '',
        'tagline'     => '',
        'title'       => ''
    ));

    # config default styles
    define( 'DEFAULT_STYLES', array(
        "background-color" => "#1C2833",
        "color" => "#F5F5F5"
    ));

    # config default widget
    define( 'DEFAULT_WIDGET', array(
        'styles' => array(
            "background-color" => "#2a2a2a",
            "color" => "#F5F5F5"
        )
    ));
