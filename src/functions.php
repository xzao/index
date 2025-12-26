<?php

    function classes_to_class($classes) {
        return implode(' ', $classes);
    }

    function fill_site_defaults($config, $site) {

        # fill site with etc defaults
        if( isset($config['defaults']['site']) ){
            $site = array_merge($config['defaults']['site'], $site);
        }


        # fill site with config defautls
        $site = array_merge(DEFAULT_SITE, $site);

        # return
        return $site;

    }

    function fill_site_image($config, $site) {

        # check exists
        if( isset($site['image']) && isset($site['image']['path']) ){
            return true;
        }

        # return image by type
        switch( $site['image']['type'] ){
            case 'svg':
                return fill_site_image_svg($config, $site);
        }

        # return default
        return DEFAULT_IMAGE;

    }

    function fill_site_image_svg($config, $site) {

        # set piece[s]
        $base = __DIR__ . '/../public';
        $core = 'vendor/selfhst-icons/svg/' . $site['image']['name'];
        $path = null;

        # set option[s]
        $options = array();

        # prepare
        if( isset($site['image']['theme']) ){
            $options[] = $core . '-' . $site['image']['theme'] .  '.svg';
        }
        $options[] = $core            .  '.svg';
        $options[] = $core . '-light' .  '.svg';
        $options[] = $core . '-dark'  .  '.svg';

        # check
        foreach( $options as $option ){
            if( file_exists($base . '/' .$option) ){
                $path = $option;
                break;
            }
        }

        # set path
        if( $path !== null ){
            $site['image']['path'] = $path;
        } else {
            $site['image'] = DEFAULT_IMAGE;
        }

        # return
        return $site;

    }    

    function fill_site_dynamics($sites) {

        # iterate
        foreach( $sites as $key => $site ){

            # fill not implemented

        }

        # return
        return $sites;

    }    

    function get_config_file($disk) {

        # file set
        $file = $disk . '/config.json';

        # file check exist[s] / fallback
        if( ! file_exists($file) ){ 
            throw new Exception('config file not found');
        }

        # return
        return $file;

    }

    function get_debug() {

        # return true
        if (isset($_ENV['INDEX_DEBUG']) && filter_var($_ENV['INDEX_DEBUG'], FILTER_VALIDATE_BOOLEAN)) {
            return true;
        }

        # return
        return false;

    }

    function get_description() {

        # return
        return get_title();

    }

    function get_protocol() {

        # default http
        $protocol = 'http';

        # check for https
        if( ! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ){ 
            $protocol = 'https';
        }

        # return
        return $protocol;
        
    }

    function get_site_html($site) {

        # print html
        echo '<div class="col s12 m12 l6 xl4 ' . classes_to_class($site['classes']) . '">';
        echo '    <a href=' . $site['link'] . '>                        ';
        echo '        <div class="card" style="'. styles_to_style($site['styles']) . '">';
        echo '            <div class="card-image darken">';
        echo '                <img class="card-image-logo" src="' . $site['image']['path'] . '">';
        echo '            </div>';
        echo '            <div class="card-content darken">';
        echo '                <div class="card-title">' . $site['title'] . '</div>';
        echo '                <p class="card-tagline">';
        echo '                   ' . $site['tagline'] . '';
        echo '                </p>';
        echo '                <br />';
        echo '                <p class="card-desc">';
        echo '                   ' . $site['description'] . '';
        echo '                </p>';
        echo '            </div>';
        echo '            <div class="card-spacer"></div>';
        echo '        </div>';
        echo '    </a>';
        echo '</div>';

    }

    function get_sites($config) {

        # check config
        if( ! isset($config['sites']) ){
            throw new Exception('invalid config: missing sites');
        }

        # initialise sites
        $sites = array();

        # iterate config sites
        foreach( $config['sites'] as $site ){
            
            # fill defaults
            $site = fill_site_defaults($config, $site);

            # fill image
            $site = fill_site_image($config, $site);

            # append
            $sites[] = $site;

        } 

        # return
        return $sites;

    }

    function get_title() {
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
        } else {
            return "Index";
        }     
    }

    function load_config($file) {

        # load file exist[s]
        if( ! file_exists($file) ){ die('Location: /error/404'); }

        # load file json
        $index_json_string = file_get_contents($file);
        if( $index_json_string === false ){ die('Location: /error/500'); }

        # load file json decode
        $index_json = json_decode($index_json_string, true);
        if( $index_json === null ){ die('Location: /error/500'); }

        # return
        return $index_json;

    }

    function print_debug($config, $sites) {

        # print
        echo '<pre>';
        echo '<hr />';
        echo '<h1>Config</h1>';
        print_r($config);
        echo '<hr />';
        echo '<h1>Server</h1>';
        print_r($_SERVER);
        echo '<hr />';
        echo '<h1>Sites</h1>';
        print_r($sites);        
        echo '<hr />';

    }

    function styles_to_style($styles) {

        # style
        $style = '';
    
        # style style[s]
        foreach ($styles as $property => $value) {
            $style .= "$property: $value; ";
        }
        
        # return
        return $style;

    }
