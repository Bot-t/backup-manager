<?php
// 参数内容为生成文件路径 此例中则在当前目录生成example.phar打包程序
$phar = new Phar(__DIR__ . '/dist/backup.phar');
// 开始打包
$phar->startBuffering();
// 要打包的目的目录 绝对路径
$phar->buildFromDirectory(__DIR__ . '/src');
// 压缩方式 GZ->gzip  BZ2->bz2
$phar->compressFiles(Phar::GZ);
// 设置启动加载脚本 即导入phar之后第一个自动执行的脚本
$phar->setStub($phar->createDefaultStub('main.php'));
// 结束打包
$phar->stopBuffering();
