<?php
/**
 * @var bool $piwik_enabled
 * @var string $piwik_domain
 * @var string $piwik_site_id
 */
?>
<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<!--[if IE 8 ]><html class="ie8"><![endif]-->
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-COMPATIBLE" content="IE=edge"/>
    <meta name="twitter:image" content="<?php echo $image; ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@socimatte">
    <meta name="twitter:creator" content="@socimatte">
    <meta name="twitter:title" content="<?php echo $trivia['category']; ?>">
    <meta name="twitter:description" content="<?php echo $description?$description:$default_description; ?>">
    <meta property="og:url"                content="<?php echo $url; ?>" />
    <meta property="og:type"               content="website" />
    <meta property="og:title"              content="<?php echo $trivia['category']; ?>" />
    <meta property="og:description"        content="<?php echo $description?$description:$default_description; ?>" />
    <meta property="og:image"              content="<?php echo $image; ?>" />    
    <title><?php echo $site_name ?></title>

    <link rel="shortcut icon" href="<?php echo base_url();?>favicon.png">

    <?php echo CssJs::getInst()->get_css() ?>
    <?php echo CssJs::getInst()->get_header_js() ?>
    <?php echo JsSettings::instance()->get_settings_string();?>
</head> 

<body>
                
    <?php echo $this->template->block('_header', 'blocks/header/trivia_header'); ?>

    <div class="page-wrapper">
        <div class="wrapper">
            <?php echo $this->template->block('_sidebar', 'blocks/sidebar/sidebar'); ?>
            <?php echo $this->template->block('_alert', 'blocks/alert'); ?>
            <div class="main">
                <?php echo $this->template->layout_yield(); ?>
            </div>
        </div>
    </div>

    <?php echo $this->template->block('_alert', 'blocks/modal/application/question_modal'); ?>
                
    <?php echo CssJs::getInst()->get_footer_js() ?>

</body>
</html>