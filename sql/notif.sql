drop table if exists notif;


create table notif (
not_id integer not null primary key auto_increment,
user_id integer(11) not null,
not_date DATETIME not null,
not_link varchar(100) not null
) engine=innodb character set utf8 collate utf8_unicode_ci;







