<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<!--[if IE 8 ]><html class="ie8"><![endif]-->
<html>

<head>
    <meta name="globalsign-domain-verification" content="nblf6SYWu5_Ro0KvO661biGL-qnI6-mSzG9_782Zud" /> 
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-COMPATIBLE" content="IE=edge"/>
    <title><?php echo $site_name ?></title>

    <link rel="shortcut icon" href="<?php echo base_url();?>favicon.png">
    <meta name="viewport" content="width=device-width, user-scalable=no">
    <?php echo CssJs::getInst()->get_css() ?>
    <?php echo CssJs::getInst()->get_header_js() ?>
    <style type="text/css">.fancybox-margin{margin-right:21px;}</style>
    <?php echo JsSettings::instance()->get_settings_string();?>
</head> 

<body>
            
    <?php echo $this->template->block('_header', 'blocks/header/auth'); ?>

    <div class="page-wrapper">
        <div class="wrapper">

            <?php echo $this->template->block('_alert', 'blocks/alert'); ?>

            <?php echo $this->template->layout_yield(); ?>

        </div>

    </div>

   <?php echo CssJs::getInst()->get_footer_js() ?>

</body>
</html>