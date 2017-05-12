drop table if exists watch;


create table watch (

    watch_id integer not null primary key auto_increment,

    watch_admin integer not null,

    watch_user integer not null
    
    

) engine=innodb character set utf8 collate utf8_unicode_ci;


