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

$backup = Config::fromPhpFile(dirname(Phar::running(false)) . '/backup.php');

$filesystems = new FilesystemProvider(Config::fromPhpFile(__DIR__ . '/storage.php'));
$filesystems->add(new LocalFilesystem);

$databases = new DatabaseProvider($backup);
$databases->add(new MysqlDatabase);

$compressors = new CompressorProvider;
$compressors->add(new GzipCompressor);

$manager = new Manager($filesystems, $databases, $compressors);

foreach (array_keys($backup->getItems()) as $dbname) {
    $manager->makeBackup()->run($dbname, [new Destination('local', $dbname . '/backup-' . date('YmdHis') . '.sql')], 'gzip');
}
