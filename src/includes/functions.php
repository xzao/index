<?php

    function app_to_html($app) {

        # fallback icon check
        if( ! isset($app['icon']) ){ $app['icon'] = array(); }
        if( ! isset($app['icon']['category']) ){ $app['icon']['category'] = 'default'; }
        if( ! isset($app['icon']['name'])     ){ $app['icon']['name'] = 'fallback'; }

        # fallback icon check
        if( ! file_exists(WWW . '/images/icons/' . $app['icon']['category'] . '/' . $app['icon']['name'] . '.png') ){
            $app['icon']['category'] = 'default';
            $app['icon']['name']     = 'fallback';
        }

        # print html
        echo '<div class="col s12 m12 l6 xl4 ' . classes_to_class($app['classes']) . '">';
        echo '    <a href=' . $app['link'] . '>                        ';
        echo '        <div class="card" style="'. styles_to_style($app['styles']) . '">';
        echo '            <div class="card-image darken">';
        echo '                <img class="card-image-logo" src="images/icons/' . $app['icon']['category'] . '/' . $app['icon']['name'] . '.png">';
        echo '            </div>';
        echo '            <div class="card-content darken">';
        echo '                <div class="card-title">' . $app['title'] . '</div>';
        echo '                <p class="card-tagline">';
        echo '                   ' . $app['tagline'] . '';
        echo '                </p>';
        echo '                <br />';
        echo '                <p class="card-desc">';
        echo '                   ' . $app['description'] . '';
        echo '                </p>';
        echo '            </div>';
        echo '            <div class="card-spacer"></div>';
        echo '        </div>';
        echo '    </a>';
        echo '</div>';

    }

    function classes_to_class($classes) {
        return implode(' ', $classes);
    }

    function fill_defaults($apps) {

        # iterate
        foreach( $apps as $key => $app ){

            # fill default[s]
            $apps[$key] = array_merge(PACKAGE_DEFAULTS, $app);

        }

        # return
        return $apps;

    }

    function fill_dynamics($apps) {

        # iterate
        foreach( $apps as $key => $app ){

            # fill not implemented

        }

        # return
        return $apps;

    }    

    function get_debug() {

        # return true
        if (isset($_ENV['INDEX_DEBUG']) && filter_var($_ENV['INDEX_DEBUG'], FILTER_VALIDATE_BOOLEAN)) {
            return true;
        }

        # return
        return false;

    }

    function get_file($disk) {

        # file set
        $file = $disk.'/servers/'.$_SERVER['SERVER_NAME'].'.json';

        # file check exist[s] / fallback
        if( ! file_exists($file) ){ 
            $file = $disk.'/default.json';
        }

        # return
        return $file;
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

    function load_file($file) {

        # load file exist[s]
        if( ! file_exists($file) ){ header('Location: /error/404'); }

        # load file json
        $index_json_string = file_get_contents($file);
        if( $index_json_string === false ){ header('Location: /error/500'); }

        # load file json decode
        $index_json = json_decode($index_json_string, true);
        if( $index_json === null ){ header('Location: /error/500'); }

        # return
        return $index_json;

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

?>
