<?php
defined( '_PHP_CONGES' ) or die( 'Restricted access' );
include TEMPLATE_PATH . 'template_define.php';

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?= $title ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="60x60" href="/apple-touch-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="76x76" href="/apple-touch-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="152x152" href="/apple-touch-icon-152x152.png">
		<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon-180x180.png">
		<link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
		<link rel="icon" type="image/png" href="/android-chrome-192x192.png" sizes="192x192">
		<link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96">
		<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
		<link rel="manifest" href="/manifest.json">
		<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
		<meta name="msapplication-TileColor" content="#da532c">
		<meta name="msapplication-TileImage" content="/mstile-144x144.png">
		<meta name="theme-color" content="#ffffff">
        <?php /* JQUERY */ ?>
        <link type="text/css" href="<?= ASSETS_PATH ?>jquery/css/custom-theme/jquery-ui-1.8.17.custom.css" rel="stylesheet" />
        <?php /* BOOTSTRAP */?>
        <link type="text/css" href="<?= ASSETS_PATH ?>bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <?php /* FONT AWESOME */ ?>
        <link href="<?= ASSETS_PATH ?>font-awesome/css/font-awesome.css" rel="stylesheet">
        <?php /* REBOOT STYLE */ ?>
        <link type="text/css" href="<?= CSS_PATH ?>reboot.css" rel="stylesheet" media="screen">
        <?php /* SCRIPTS */ ?>
        <script type="text/javascript" src="<?= ASSETS_PATH ?>jquery/js/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="<?= ASSETS_PATH; ?>jquery/js/jquery-ui-1.8.17.custom.min.js"></script>
        <script type="text/javascript" src="<?= ASSETS_PATH ?>jquery/js/jquery.tablesorter.min.js"></script>
        <script type="text/javascript" src="<?= JS_PATH ?>reboot.js"></script>
        <?= $additional_head ?>
    </head>
    <body class="popup">
