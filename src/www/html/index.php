<?php require_once(__DIR__.'/config.php');    ?>
<?php require_once(__DIR__.'/functions.php'); ?>
<?php

    # app[s]
    $file = get_file(DISK);
    $apps = load_file($file);
    $apps = fill_defaults($apps);
    $apps = fill_dynamics($apps);

    # debug
    if( DEBUG ){
        echo '<pre>';
        echo '<hr />';
        echo '<h1>APPS</h1>';
        print_r($apps);       
        echo '<hr />';        
        echo '<h1>SERVER</h1>';
        print_r($_SERVER);
        echo '<hr />';
        die;
    }

?>
<!DOCTYPE html>
<html lang="en">
    <head>

        <!-- title -->
        <title><?php echo TITLE; ?></title>

        <!-- meta[s] -->
        <meta name="viewport"           content="width=device-width, initial-scale=1">
        <meta name="description"        content="<?php echo TITLE; ?>">
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
        <div class="page">
            <div class="row">
                <?php foreach( $apps as $app ){ ?>

                    <!-- app -->
                    <?php app_to_html($app); ?>

                <?php } ?>
                <?php if( EDIT ){ ?>

                    <!-- app - edit -->
                    <?php app_to_html(PACKAGE_DEFAULTS_ADD); ?>

                <?php } ?>
            </div>
        <div>

        <!-- script[s]. -->
        <script src="vendor/jquery.min.js"></script>
        <script src="vendor/materialize.min.js"></script>
        <script src="script.js"></script>

    </body>
</html>
