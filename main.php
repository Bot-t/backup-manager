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
use GuzzleHttp\Client;

function notify(string $content)
{
    $configFile = dirname(Phar::running(false)) . '/skype.json';
    if (!file_exists($configFile)) {
        return;
    }

    $config = new Config(json_decode(file_get_contents($configFile), true));

    try {
        $client = new Client([
            'base_uri' => $config->get('url'),
            'headers' => [
                'Authorization' => $config->get('token'),
            ],
        ]);

        $client->post('/api/notify', [
            'json' => [
                [
                    'cid' => $config->get('cid'),
                    'content' => $content,
                ],
            ],
        ]);
    } catch (Throwable $e) {
    }
}

// 检查数据库连接并过滤
$dbConfigAry = json_decode(file_get_contents(dirname(Phar::running(false)) . '/database.json'), true);
foreach ($dbConfigAry as $dbname => $config) {
    $dsn = "{$config['type']}:host={$config['host']};port={$config['port']};dbname={$config['database']}";
    try {
        new PDO($dsn, $config['user'], $config['pass']);
    } catch (Throwable $e) {
        unset($dbConfigAry[$dbname]);
        echo '数据库[' . $dbname . ']连接失败：' . $e->getMessage() . PHP_EOL;
        $msg = <<<EOL
【异常通知】- Backup
════════════════════════
{$dbname}:{$e->getMessage()}
EOL;
        notify($msg);
    }
}

if (empty($dbConfigAry)) {
    echo '没有可用配置' . PHP_EOL;
    exit;
}

$backupConfig = new Config($dbConfigAry);

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
    try {
        echo $dbname . '备份中...' . PHP_EOL;
        $manager->makeBackup()->run($dbname, $destinations, 'gzip');
    } catch (Throwable $e) {
    }
}
