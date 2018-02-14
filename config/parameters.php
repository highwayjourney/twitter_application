<?php

$config['filesystem.base.path'] = FCPATH;

$config['email.config'] = array(
    'from' => array(
        'email' => 'support@autosoci.com',
        'name' => 'autosoci'
    ),
    'options' => array(

    ),
    'mail_transport' => array(
        'type' => 'sendmail',
        'smtp_config' => array(
            'host' => 'autosoci.com',
            'port' => '465',
            'username' => 'support@autosoci.com',
            'password' => '{H;mgkd]RRL}'
        )
    ),

    'templates_config' => array(
        'path' => 'templates/email',
        'layout' => 'layout',
    )
);

