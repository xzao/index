<?php

    function app_to_html($app) {

        # fallback logo
        if( ! file_exists(WWW . '/images/logos/' . $app['logo']) ){
            $app['logo'] = 'base.app.index.fallback.png';
        }

        # print html
        echo '<div class="col s12 m12 l6 xl4 ' . classes_to_class($app['classes']) . '">';
        echo '    <a href=' . $app['link'] . '>                        ';
        echo '        <div class="card" style="'. styles_to_style($app['styles']) . '">';
        echo '            <div class="card-image darken">';
        echo '                <img class="card-image-logo" src="images/logos/' . $app['logo'] . '">';
        echo '            </div>';
        echo '            <div class="card-content darken">';
        echo '                <div class="card-title">' . $app['title'] . '</div>';
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
        }

        # return
        return $apps;

    }    

    function get_file($disk) {
        return $disk.'/index.'.$_SERVER['SERVER_NAME'].'.json';
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
