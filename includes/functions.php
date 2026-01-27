<?php
#
#   src/functions.php
#


#
#   collapse[er]s
#
function collapse_classes_to_class($classes) {

    # return
    return implode(' ', $classes);

}

function collapse_styles_to_style($styles) {

    # style
    $style = '';

    # style style[s]
    foreach ($styles as $property => $value) {
        $style .= "$property: $value; ";
    }
    
    # return
    return $style;

}


#
#   fill[er]s
#
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

function fill_site_group($group, $site) {

    # extract group properties
    $group_properties = $group;
    unset($group_properties['sites']);

    # fill site with group properties
    $site = array_merge($group_properties, $site);

    # merge filters deeply so both group and site filters apply
    if( isset($group_properties['filters']) && isset($site['filters']) ){
        $merged_filters = array();
        
        # merge each filter type (domains, ips, ports)
        foreach( array('domains', 'ips', 'ports') as $filter_type ){
            $group_values = isset($group_properties['filters'][$filter_type]) ? $group_properties['filters'][$filter_type] : array();
            $site_values = isset($site['filters'][$filter_type]) ? $site['filters'][$filter_type] : array();
            
            # merge and remove duplicates
            if( !empty($group_values) || !empty($site_values) ){
                $merged_filters[$filter_type] = array_values(array_unique(array_merge($group_values, $site_values)));
            }
        }
        
        $site['filters'] = $merged_filters;
    }

    # return
    return $site;

}

function fill_site_image_path($path) {

    # prepend ../icons/ to paths starting with vendor/ or custom/
    if( strpos($path, 'vendor/') === 0 || strpos($path, 'custom/') === 0 ){
        return '../icons/' . $path;
    }

    # return
    return $path;

}

function fill_site_image($config, $site) {

    # check exists
    if( isset($site['image']) && isset($site['image']['path']) ){
        return $site;
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

    # set image by type
    switch( $site['image']['type'] ){
        case 'svg':
            $site = fill_site_image_svg($config, $site);
    }

    # check
    if( ! file_exists(__DIR__ . '/' . fill_site_image_path($site['image']['path'])) ){
        $site['image'] = DEFAULT_IMAGE;
    }

    # return
    return $site;

}

function fill_site_image_svg($config, $site) {

    # set piece[s]
    $base = __DIR__;
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
        if( file_exists($base . '/' . fill_site_image_path($option)) ){
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


#
#   filter[s]
#
function filter_sites_by_domain($sites, $domain = null) {

    # set domain if not set
    if( $domain === null ){
        if( isset($_SERVER['HTTP_HOST']) ){
            $host = $_SERVER['HTTP_HOST'];
            $domain = explode(':', $host)[0];
        } else {
            $domain = $_SERVER['SERVER_NAME'] ?? 'localhost';
        }
    }

    # filter sites var
    $sites_filtered = array();

    # filter sites
    foreach( $sites as $site ){

        # check if site has domain filters, if no domain filter, include by default
        if( isset($site['filters']['domains']) && is_array($site['filters']['domains']) ){
            if( in_array($domain, $site['filters']['domains']) ){
                $sites_filtered[] = $site;
            }
        } else {
            $sites_filtered[] = $site;
        }

    }

    # return
    return $sites_filtered;

}

function filter_sites_by_ip($sites, $ip = null) {

    # set ip if not set
    if( $ip === null ){
        if( isset($_SERVER['REMOTE_ADDR']) ){
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = '127.0.0.1';
        }
    }

    # determine ip version and filter accordingly
    if( filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ){
        return filter_sites_by_ipv4($sites, $ip);
    } elseif( filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ){
        return filter_sites_by_ipv6($sites, $ip);
    }

    # return all sites if ip is invalid
    return $sites;

}

function filter_sites_by_ipv4($sites, $ipv4 = null) {

    # set ipv4 if not set
    if( $ipv4 === null ){
        if( isset($_SERVER['REMOTE_ADDR']) ){
            $ipv4 = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipv4 = '127.0.0.1';
        }
    }

    # validate ipv4
    if( ! filter_var($ipv4, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ){
        return $sites;
    }

    # filter sites var
    $sites_filtered = array();

    # filter sites
    foreach( $sites as $site ){

        # check if site has ip filters, if no ip filter, include by default
        if( isset($site['filters']['ips']) && is_array($site['filters']['ips']) ){
            $matched = false;
            foreach( $site['filters']['ips'] as $filter_ip ){
                if( ip_match_ipv4($ipv4, $filter_ip) ){
                    $matched = true;
                    break;
                }
            }
            if( $matched ){
                $sites_filtered[] = $site;
            }
        } else {
            $sites_filtered[] = $site;
        }

    }

    # return
    return $sites_filtered;

}

function filter_sites_by_ipv6($sites, $ipv6 = null) {

    # set ipv6 if not set
    if( $ipv6 === null ){
        if( isset($_SERVER['REMOTE_ADDR']) ){
            $ipv6 = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipv6 = '::1';
        }
    }

    # validate ipv6
    if( ! filter_var($ipv6, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ){
        return $sites;
    }

    # filter sites var
    $sites_filtered = array();

    # filter sites
    foreach( $sites as $site ){

        # check if site has ip filters, if no ip filter, include by default
        if( isset($site['filters']['ips']) && is_array($site['filters']['ips']) ){
            $matched = false;
            foreach( $site['filters']['ips'] as $filter_ip ){
                if( ip_match_ipv6($ipv6, $filter_ip) ){
                    $matched = true;
                    break;
                }
            }
            if( $matched ){
                $sites_filtered[] = $site;
            }
        } else {
            $sites_filtered[] = $site;
        }

    }

    # return
    return $sites_filtered;

}

function filter_sites_by_port($sites, $port = null) {

    # set port if not set
    if( $port === null ){
        if( isset($_SERVER['SERVER_PORT']) ){
            $port = (int)$_SERVER['SERVER_PORT'];
        } else {
            $port = 80;
        }
    }

    # filter sites var
    $sites_filtered = array();

    # filter sites
    foreach( $sites as $site ){

        # check if site has port filters, if no port filter, include by default
        if( isset($site['filters']['ports']) && is_array($site['filters']['ports']) ){
            if( in_array($port, $site['filters']['ports']) ){
                $sites_filtered[] = $site;
            }
        } else {
            $sites_filtered[] = $site;
        }

    }

    # return
    return $sites_filtered;

}


#
#   generate[er]s
#
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
    $svg_path = __DIR__ . '/' . fill_site_image_path($site['image']['path']);

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

    # generate slightly darker background for blended look (80% of original)
    $darken_factor = 0.80;
    $bg_r = round($r * $darken_factor);
    $bg_g = round($g * $darken_factor);
    $bg_b = round($b * $darken_factor);
    
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


#
#   get[er]s
#
function get_body_styles($config) {

    # check config
    if( isset($config['defaults']['body']['styles']) ){
        return $config['defaults']['body']['styles'];
    }

    # return
    return array();

}

function get_config_file($disk) {

    # file set
    $file = $disk . '/index.json';

    # file check exist[s] / fallback
    if( ! file_exists($file) ){ 
        throw new Exception('index file not found');
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

function get_page_styles($config) {

    # check config
    if( isset($config['defaults']['page']['styles']) ){
        return $config['defaults']['page']['styles'];
    }

    # return
    return array();

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

function get_site_image_data_uri($site) {

    # check if image path exists
    if( ! isset($site['image']['path']) || empty($site['image']['path']) ){
        return '';
    }

    # build full path
    $svg_full_path = __DIR__ . '/' . fill_site_image_path($site['image']['path']);

    # check file exists
    if( ! file_exists($svg_full_path) ){
        return $site['image']['path'];
    }

    # read file content
    $content = file_get_contents($svg_full_path);
    if( $content === false ){
        return $site['image']['path'];
    }

    # determine mime type
    $mime_type = 'image/svg+xml';
    if( isset($site['image']['type']) ){
        switch( $site['image']['type'] ){
            case 'png':
                $mime_type = 'image/png';
                break;
            case 'jpg':
            case 'jpeg':
                $mime_type = 'image/jpeg';
                break;
            case 'webp':
                $mime_type = 'image/webp';
                break;
        }
    }

    # encode and return data uri
    $base64 = base64_encode($content);

    # return
    return 'data:' . $mime_type . ';base64,' . $base64;

}

function get_sites($config) {

    # check config
    if( ! isset($config['groups']) ){
        throw new Exception('invalid config: missing groups');
    }

    # sites init
    $sites = array();

    # sites populate
    foreach( $config['groups'] as $group ){
        
        # check group has sites
        if( ! isset($group['sites']) || ! is_array($group['sites']) ){
            continue;
        }

        # iterate sites in group
        foreach( $group['sites'] as $site ){
            
            # fill group
            $site = fill_site_group($group, $site);

            # fill image
            $site = fill_site_image($config, $site);

            # fill styles
            $site = fill_site_styles($config, $site);

            # fill defaults
            $site = fill_site_defaults($config, $site);

            # append
            $sites[] = $site;

        }

    }

    # sites filter
    $sites = filter_sites_by_ip($sites);
    $sites = filter_sites_by_domain($sites);
    $sites = filter_sites_by_port($sites);

    # return
    return $sites;

}

function get_title() {

    # check required
    if( ! isset($_SERVER['HTTP_HOST']) ){
        return "Index";
    }

    # prepare title
    $title = explode(':', $_SERVER['HTTP_HOST'])[0];
    $title = explode('.', $title);
    if( count($title) > 1 ){
        array_pop($title);
    }
    foreach( $title as $key => $value ){
        $title[$key] = ucfirst($value);
    }
    $title = implode(" - ", $title);

    # return
    return $title;

}


#
#   ip match[s]
#
function ip_match_ipv4($ip, $cidr) {

    # exact match
    if( $ip === $cidr ){
        return true;
    }

    # check if cidr notation
    if( strpos($cidr, '/') === false ){
        return false;
    }

    # parse cidr
    list($subnet, $mask) = explode('/', $cidr);

    # validate subnet
    if( ! filter_var($subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ){
        return false;
    }

    # validate mask
    if( ! is_numeric($mask) || $mask < 0 || $mask > 32 ){
        return false;
    }

    # convert ips to long
    $ip_long = ip2long($ip);
    $subnet_long = ip2long($subnet);

    # create mask
    $mask_long = -1 << (32 - (int)$mask);

    # apply mask and compare
    return ($ip_long & $mask_long) === ($subnet_long & $mask_long);

}

function ip_match_ipv6($ip, $cidr) {

    # exact match
    if( $ip === $cidr ){
        return true;
    }

    # check if cidr notation
    if( strpos($cidr, '/') === false ){
        return false;
    }

    # parse cidr
    list($subnet, $mask) = explode('/', $cidr);

    # validate subnet
    if( ! filter_var($subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ){
        return false;
    }

    # validate mask
    if( ! is_numeric($mask) || $mask < 0 || $mask > 128 ){
        return false;
    }

    # convert ipv6 to binary
    $ip_bin = inet_pton($ip);
    $subnet_bin = inet_pton($subnet);

    # create mask
    $mask_int = (int)$mask;
    $ip_bits = '';
    $subnet_bits = '';

    # convert to binary string
    for( $i = 0; $i < strlen($ip_bin); $i++ ){
        $ip_bits .= str_pad(decbin(ord($ip_bin[$i])), 8, '0', STR_PAD_LEFT);
        $subnet_bits .= str_pad(decbin(ord($subnet_bin[$i])), 8, '0', STR_PAD_LEFT);
    }

    # compare masked bits
    return substr($ip_bits, 0, $mask_int) === substr($subnet_bits, 0, $mask_int);

}


#
#   load[er]s
#
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


#
#   print[er]s
#
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


#
#   render[er]s
#
function render_site_html($site, $html = '') {

    # html build
    $html .= '<div class="col s12 m12 l6 xl4 ' . collapse_classes_to_class($site['classes']) . '">';
    $html .= '    <a href=' . $site['link'] . '>                        ';
    $html .= '        <div class="card" style="'. collapse_styles_to_style($site['styles']) . '">';
    $html .= '            <div class="card-image darken">';
    $html .= '                <img class="card-image-logo" src="' . get_site_image_data_uri($site) . '">';
    $html .= '            </div>';
    $html .= '            <div class="card-content darken">';
    $html .= '                <div class="card-title">' . $site['title'] . '</div>';
    $html .= '                <p class="card-tagline">';
    $html .= '                   ' . $site['tagline'] . '';
    $html .= '                </p>';
    $html .= '                <br />';
    $html .= '                <p class="card-desc">';
    $html .= '                   ' . $site['description'] . '';
    $html .= '                </p>';
    $html .= '            </div>';
    $html .= '            <div class="card-spacer"></div>';
    $html .= '        </div>';
    $html .= '    </a>';
    $html .= '</div>';

    # return
    return $html;

}
