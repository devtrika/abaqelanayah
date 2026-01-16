<?php
return [

    'driver'     => 'sendmail',
    'host'       => 'mail.smtp2go.com',
    'port'       => env('MAIL_PORT'),
    'from'       => array('address' => env('MAIL_FROM_ADDRESS') , 'name' => env('MAIL_FROM_NAME')),
    'encryption' => env('MAIL_ENCRYPTION'),
    'username'   => env('MAIL_USERNAME'),
    'password'   => env('MAIL_PASSWORD'),
    'sendmail'   => '/usr/sbin/sendmail -bs',
    'pretend'    => false,
];