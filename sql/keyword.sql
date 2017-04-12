drop table if exists keyword;


create table keyword (
keyword_id integer not null primary key auto_increment,
user_id integer(11) not null,
keyword varchar(100) not null
) engine=innodb character set utf8 collate utf8_unicode_ci;







insert into keyword values
(1,1, 'playstation plus');
