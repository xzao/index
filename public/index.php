<?php require_once(__DIR__ . '/../includes/functions.php'); ?>
<?php require_once(__DIR__ . '/../includes/config.php');    ?>
<?php

    # config
    $config_file = get_config_file(ETC);
    $config      = load_config($config_file);

    # site[s]
    $sites = get_sites($config);

    # page styles
    $page_styles = get_page_styles($config);
    $page_style  = collapse_styles_to_style($page_styles);

    # debug
    if( DEBUG ){ print_debug($config, $sites); }

?>
<!DOCTYPE html>
<html lang="en">
    <head>

        <!-- title -->
        <title><?php echo TITLE; ?></title>

        <!-- meta[s] -->
        <meta name="viewport"           content="width=device-width, initial-scale=1">
        <meta name="description"        content="<?php echo DESCRIPTION; ?>">
        <meta name="theme-color"        content="#1d1d1d">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta property="og:title"       content="<?php echo TITLE; ?>">
        <meta property="og:type"        content="website">
        <meta property="og:url"         content="<?php echo HOST; ?>">
        <meta property="og:description" content="">
        <meta property="og:image"       content="image.png">

        <!-- link[s]  -->
        <link rel="icon" href="/favicon.ico">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        <link rel="stylesheet" href="vendor/materialize.min.css">
        <link rel="stylesheet" href="styles.css">

    </head>
    <body>

        <!-- container -->
        <div class="page" style="<?php echo $page_style; ?>">
            <div class="row">
                <?php foreach( $sites as $site ){ ?>

                    <!-- site -->
                    <?php echo render_site_html($site); ?>

                <?php } ?>
            </div>
        <div>

        <!-- script[s]. -->
        <script src="vendor/jquery.min.js"></script>
        <script src="vendor/materialize.min.js"></script>
        <script src="script.js"></script>

    </body>
</html>
