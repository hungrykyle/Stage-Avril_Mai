drop table if exists lien_annonce;


create table lien_annonce (
lien_id integer not null primary key auto_increment,
lien_id_annonce integer(11) not null,
lien_link varchar(100) not null,
lien_title varchar(100) not null

) engine=innodb character set utf8 collate utf8_unicode_ci;




insert into lien_annonce values
(1,1,'Le lien de l abonnement', 'Abonnez-vous');
