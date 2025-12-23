<?php require_once(__DIR__.'/includes/functions.php'); ?>
<?php

    # config
    define( 'DEBUG',    get_debug()                            );
    define( 'ETC',      realpath('/etc/index')                 );
    define( 'HOST',     explode(':', $_SERVER['HTTP_HOST'])[0] );
    define( 'IGNORE',   array('.', '..', '.gitignore')         );
    define( 'PROTOCOL', get_protocol()                         ); 
    define( 'ROOT',     realpath(__DIR__ .  '/..')             );
    define( 'SRC',     realpath(__DIR__)                       );
    define( 'TITLE',    get_title()                            );
    define( 'WWW',      realpath(__DIR__ . '/public')          );

    # config package default[s]
    define( 'PACKAGE_DEFAULTS', array(
        'classes' => array(),
        'logo'    => 'base.app.index.png',
        'styles'  => array(
            'background-color' => 'white',
            'border-color'     => 'white',            
            'color'            => '#212427'
        )
    )); 

    # config package type[s]
    define( 'PACKAGE_TYPES', array(
        'app', 'room'
    ));
  
?>
