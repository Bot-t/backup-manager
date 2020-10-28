# backup-manager
数据库备份管理

## 安装

```bash
mkdir /var/backup && cd /var/backup
wget https://github.com/Bot-t/backup-manager/raw/main/dist/backup.phar
cat > database.php <<EOL
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
    ],
];
EOL
```

## 使用
```bash
php backup.phar
```
