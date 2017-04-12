drop table if exists extra;


create table extra (
extra_id integer not null primary key auto_increment,
extra_id_annonce integer(11) not null,
extra_text varchar(100) not null

) engine=innodb character set utf8 collate utf8_unicode_ci;




insert into extra values
(1,1, 'Information supplémentaire');
