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

        # set fallback
        if( ! isset($site['image']) ){
            $site['image'] = array();
        }

        # set fallback type
        if( ! isset($site['image']['name']) ){
            $site['image']['name'] = strtolower($site['id']);
        }

        # set fallback type
        if( ! isset($site['image']['type']) ){
            $site['image']['type'] = 'svg';
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

    function fill_site_styles($config, $site) {

        # check exists
        if( isset($site['styles']) ){
            return $site;
        }

        # set styles
        $styles = generate_styles_from_site($site);

        # fill
        $site['styles'] = $styles;

        # return
        return $site;

    }

    function generate_styles_from_site($site) {
        
        # set styles
        $styles = null;

        # generate
        switch( $site['image']['type'] ){
            case 'svg':
                $styles = generate_styles_from_site_svg($site);
        }

        # default
        if( $styles === null ){
            $styles = DEFAULT_STYLES;
        }

        # return
        return $styles;

    }

    function generate_styles_from_site_svg($site) {

        # check path exists
        if( ! isset($site['image']['path']) || empty($site['image']['path']) ){
            return DEFAULT_STYLES;
        }

        # build full path
        $svg_path = __DIR__ . '/../public/' . $site['image']['path'];

        # check file exists
        if( ! file_exists($svg_path) ){
            return DEFAULT_STYLES;
        }

        # read svg content
        $svg_content = file_get_contents($svg_path);
        if( $svg_content === false ){
            return DEFAULT_STYLES;
        }

        # extract colors from svg using regex
        $colors = array();
        $patterns = array(
            '/fill="(#[0-9A-Fa-f]{6})"/i',
            '/fill:(#[0-9A-Fa-f]{6})/i',
            '/stroke="(#[0-9A-Fa-f]{6})"/i',
            '/stroke:(#[0-9A-Fa-f]{6})/i'
        );

        foreach( $patterns as $pattern ){
            if( preg_match_all($pattern, $svg_content, $matches) ){
                foreach( $matches[1] as $color ){
                    $color = strtoupper($color);
                    # skip common neutrals
                    if( $color !== '#FFFFFF' && $color !== '#000000' && $color !== '#FFF' && $color !== '#000' ){
                        if( ! in_array($color, $colors) ){
                            $colors[] = $color;
                        }
                    }
                }
            }
        }
        
        # check if we found any colors
        if( empty($colors) ){
            return DEFAULT_STYLES;
        }

        # get dominant color (first color found) and convert to RGB
        $hex = ltrim($colors[0], '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        # generate lighter background by blending with white (85% lighter)
        $white = 255;
        $bg_r = round($r + ($white - $r) * 0.85);
        $bg_g = round($g + ($white - $g) * 0.85);
        $bg_b = round($b + ($white - $b) * 0.85);
        
        # clamp and convert to hex
        $bg_r = max(0, min(255, $bg_r));
        $bg_g = max(0, min(255, $bg_g));
        $bg_b = max(0, min(255, $bg_b));
        $bg_color = sprintf("#%02X%02X%02X", $bg_r, $bg_g, $bg_b);

        # determine text color based on background brightness
        $brightness = ($bg_r * 299 + $bg_g * 587 + $bg_b * 114) / 1000;
        $text_color = ($brightness > 128) ? '#2B2B2B' : '#F5F5F5';

        # return styles
        return array(
            "background-color" => $bg_color,
            "color" => $text_color
        );

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

            # fill styles
            $site = fill_site_styles($config, $site);

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
