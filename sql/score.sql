drop table if exists score;


create table score (
score_id integer not null primary key auto_increment,
score_id_annonce integer(11) not null,
score_note varchar (11) not null
) engine=innodb character set utf8 collate utf8_unicode_ci;







insert into score values
(1,1,4.5);
