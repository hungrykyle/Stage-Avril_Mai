drop table if exists rapport;


create table rapport (
rap_id integer not null primary key auto_increment,
user_id integer(11) not null,
rap_keywords string(100) not null,
rap_date DATE not null,
rap_link varchar(100) not null
) engine=innodb character set utf8 collate utf8_unicode_ci;







