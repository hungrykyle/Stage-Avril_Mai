create database if not exists stage character set utf8 collate utf8_unicode_ci;

use stage;


grant all privileges on stage.* to 'default_user'@'localhost' identified by 'secret';