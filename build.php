<?php
// 参数内容为生成文件路径 此例中则在当前目录生成example.phar打包程序
$phar = new Phar(__DIR__ . '/dist/backup.phar');
$phar->startBuffering();
$phar->buildFromDirectory(__DIR__);
$phar->compressFiles(Phar::GZ);
$phar->setStub($phar->createDefaultStub('main.php'));
$phar->stopBuffering();
