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
use BackupManager\Filesystems\SftpFilesystem;
use BackupManager\Manager;

$databaseJson = file_get_contents(dirname(Phar::running(false)) . '/database.json');
$backupConfig = new Config(json_decode($databaseJson, true));

$storageJson = file_get_contents(dirname(Phar::running(false)) . '/storage.json');
$fileConfig = new Config(json_decode($storageJson, true));

$filesystems = new FilesystemProvider($fileConfig);
$filesystems->add(new LocalFilesystem);
$filesystems->add(new SftpFilesystem);

$databases = new DatabaseProvider($backupConfig);
$databases->add(new MysqlDatabase);

$compressors = new CompressorProvider;
$compressors->add(new GzipCompressor);

$manager = new Manager($filesystems, $databases, $compressors);

foreach ($backupConfig->getItems() as $dbname => $config) {
    $destinations = [new Destination('sftp', $dbname . '/backup-' . date('YmdHis') . '.sql')];
    $manager->makeBackup()->run($dbname, $destinations, 'gzip');
}
