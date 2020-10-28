<?php
return [
    'local' => [
        'type' => 'Local',
        'root' => dirname(Phar::running(false)) . '/data/',
    ],
];
