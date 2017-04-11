drop table if exists note;


create table note (
note_id integer not null primary key auto_increment,
ann_id integer(11) not null,
note integer(100) not null
) engine=innodb character set utf8 collate utf8_unicode_ci;







insert into note values
(1,1, '4');
