drop table if exists mini_annonce;


create table mini_annonce (
mini_id integer not null primary key auto_increment,
mini_id_annonce integer(11) not null,
mini_title varchar(100) not null,
mini_link varchar(100) not null,
mini_desc varchar(100) not null
) engine=innodb character set utf8 collate utf8_unicode_ci;







insert into mini_annonce values
(1,1, 'Nike Bohnomme','http://LeLienDesBohnommes.com','Pour les pieds de bohnomme');
