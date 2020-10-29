# backup-manager
数据库备份管理

## 安装

```bash
mkdir /var/backup && cd /var/backup
wget https://github.com/Bot-t/backup-manager/raw/main/dist/backup.phar
```

## 使用
`backup.phar`同级目录新建`storage.json`存储配置文件。  
`backup.phar`同级目录新建`database.json`数据库配置文件。

执行命令：
```bash
php backup.phar
```
