drop table if exists user;


create table user (

    usr_id integer not null primary key auto_increment,

    usr_name varchar(50) not null,

    usr_password varchar(88) not null,
    
    usr_mail varchar(120) not null,

    usr_salt varchar(23) not null,

    usr_role varchar(50) not null 

) engine=innodb character set utf8 collate utf8_unicode_ci;


insert into user values
(1,'HungryKyle_1', '$2y$13$qOvvtnceX.TjmiFn4c4vFe.hYlIVXHSPHfInEG21D99QZ6/LM70xa', 'Maxime1306@hotmail.fr', 'dhMTBkzwDKxnD;4KNs,4ENy','ROLE_USER'),
(2,'HungryKyle_2', '$2y$13$qOvvtnceX.TjmiFn4c4vFe.hYlIVXHSPHfInEG21D99QZ6/LM70xa', 'Maxime1306@hotmail.fr', 'dhMTBkzwDKxnD;4KNs,4ENy','ROLE_USER'),
/* raw password is '@dm1n' */
(3, 'admin', '$2y$13$A8MQM2ZNOi99EW.ML7srhOJsCaybSbexAj/0yXrJs4gQ/2BqMMW2K', 'Maxime1306@hotmail.fr', 'EDDsl&fBCJB|a5XUtAlnQN8', 'ROLE_ADMIN');