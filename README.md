# backup-manager
数据库备份管理

## 打包
```bash
php build.php
```
> 执行打包命令之后会在dist目录下生成phar文件

## 安装

```bash
mkdir /var/backup && cd /var/backup
wget https://github.com/Bot-t/backup-manager/raw/main/dist/backup.phar
```

## 使用
`backup.phar`同级目录新建`storage.json`存储配置文件。  
`backup.phar`同级目录新建`database.json`数据库配置文件。  
[可选]如果需要通知skype，则`backup.phar`同级目录新建`skype.json`skype配置文件。  
执行命令：
```bash
php backup.phar
```

## 定期清理
将`clear.sh`文件放入备份文件根目录，然后定期执行即可。
