drop table if exists annonce;


create table annonce (
ann_id integer not null primary key auto_increment,
user_id integer(11) not null,
keyword_id integer(11) not null,
ann_title varchar(100) not null,
ann_link varchar(100) not null,
ann_desc varchar(100) not null,
ann_nav varchar(100) not null,
ann_date DATE not null
) engine=innodb character set utf8 collate utf8_unicode_ci;







insert into annonce values
(1,1,1, 'Jouez davantage sur PS Plus - Obtenez 2 jeux PS4 chaque mois','www.playstation.com/PlayStation_+',
'En tant qu abonné PS Plus, enrichissez chaque mois votre collection de jeux','Opera','2002-06-12');
