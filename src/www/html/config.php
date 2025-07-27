<?php require_once(__DIR__.'/functions.php'); ?>
<?php

    # config default[s]
    $debug    = false;
    $dir_disk = '/etc/index';
    $dir_sys  = '/etc/index';
    $dir_www  = '/var/www/html';
    $edit     = false;
    $protocol = 'http';
    $title    = 'NYX';

    # config edit
    if( isset($_SERVER['REDIRECT_URL']) ){
        if( $_SERVER['REDIRECT_URL'] == '/edit' ){
            $edit = true;
        }
    }

    # config ssl
    if( ! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ){ 
        $protocol = 'https';
    }

    # config sys [dev]
    if( is_dir('/workspaces/nyx/sys') ){ 
        $dir_sys = '/workspaces/nyx/sys';
    }

    # config title
    if( isset($_SERVER['HTTP_HOST']) ){
        $title = explode(':', $_SERVER['HTTP_HOST'])[0];
        $title = explode('.', $title);
        if( count($title) > 1 ){
            array_pop($title);
        }
        foreach( $title as $key => $value ){
            $title[$key] = ucfirst($value);
        }
        $title = implode(" | ", $title);
    }

    # config
    define( 'DEBUG',    $debug                                 );    
    define( 'DISK',     $dir_disk                              );
    define( 'EDIT',     $edit                                  );
    define( 'HOST',     explode(':', $_SERVER['HTTP_HOST'])[0] );
    define( 'IGNORE',   array('.', '..', '.gitignore')         );
    define( 'NAME',     'base.app.index'                       );
    define( 'PROTOCOL', $protocol                              ); 
    define( 'ROOT',     __DIR__                                );
    define( 'SITE',     'NYX'                                  );
    define( 'SYS',      $dir_sys                               );
    define( 'TITLE',    $title                                 );
    define( 'WWW',      $dir_www                               );

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

    # config package default[s] add
    define( 'PACKAGE_DEFAULTS_ADD', array(
        'classes'     => array('add'),
        'description' => '',
        'logo'        => 'base.app.index.add.png',
        'styles'      => array(
            'background-color' => 'white',
            'border-color'     => 'white',            
            'color'            => '#212427'
        ),
        'title' => 'Add'
    ));    

    # config package type[s]
    define( 'PACKAGE_TYPES', array(
        'app', 'room'
    ));
  
?>
