<?php
return [
    'db1' => [
        'type' => 'mysql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'user' => 'root',
        'pass' => '123456',
        'database' => 'dbname',
        'singleTransaction' => false,
        'num' => 15, // 保留备份文件数量，默认15个
    ],
];
