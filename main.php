<?php
require __DIR__ . '/vendor/autoload.php';

use BackupManager\Compressors\CompressorProvider;
use BackupManager\Compressors\GzipCompressor;
use BackupManager\Config\Config;
use BackupManager\Databases\DatabaseProvider;
use BackupManager\Databases\MysqlDatabase;
use BackupManager\Filesystems\Destination;
use BackupManager\Filesystems\FilesystemProvider;
use BackupManager\Filesystems\LocalFilesystem;
use BackupManager\Manager;

$backupConfig = Config::fromPhpFile(dirname(Phar::running(false)) . '/database.php');

$fileConfig = new Config([
    'local' => [
        'type' => 'Local',
        'root' => dirname(Phar::running(false)) . '/data/',
    ],
]);

$filesystems = new FilesystemProvider($fileConfig);
$filesystems->add(new LocalFilesystem);

$databases = new DatabaseProvider($backupConfig);
$databases->add(new MysqlDatabase);

$compressors = new CompressorProvider;
$compressors->add(new GzipCompressor);

$manager = new Manager($filesystems, $databases, $compressors);

foreach ($backupConfig->getItems() as $dbname => $config) {
    $destinations = [new Destination('local', $dbname . '/backup-' . date('YmdHis') . '.sql')];
    $manager->makeBackup()->run($dbname, $destinations, 'gzip');
    $num = $config['num'] ?? 15;
    // TODO: 删除多余数量
}
