/*
SQLyog Ultimate - MySQL GUI v8.2 
MySQL - 5.5.5-10.1.25-MariaDB 
*********************************************************************
*/
/*!40101 SET NAMES utf8 */;

create table `users` (
	`id` int NOT NULL AUTO_INCREMENT,
	`name` varchar (765),
	`email` varchar (2295),
	`password_hash` varchar (2295),
	`role` varchar (450),
	`advisory` varchar(255) DEFAULT NULL,
	PRIMARY KEY (`id`)
); 
insert into `users` (`id`, `name`, `email`, `password_hash`, `role`, `advisory`) values('1','Khian Kervy Mamaril','adviser@gmail.com','$2y$10$ks046Q3Rr3wXUqYL.W56eurAk6WJ5JJlP9dj5fh.GtRyHlOz50IH6','adviser','10 - A');
insert into `users` (`id`, `name`, `email`, `password_hash`, `role`) values('2','Aron Diolata','teacher@gmail.com','$2y$10$ks046Q3Rr3wXUqYL.W56eurAk6WJ5JJlP9dj5fh.GtRyHlOz50IH6','teacher');
insert into `users` (`id`, `name`, `email`, `password_hash`, `role`) values('3','Bea Jane Baroa','principal@gmail.com','$2y$10$ks046Q3Rr3wXUqYL.W56eurAk6WJ5JJlP9dj5fh.GtRyHlOz50IH6','principal');
insert into `users` (`id`, `name`, `email`, `password_hash`, `role`) values('4','Manarang John Paul','admin@gmail.com','$2y$10$ks046Q3Rr3wXUqYL.W56eurAk6WJ5JJlP9dj5fh.GtRyHlOz50IH6','admin');
insert into `users` (`id`, `name`, `email`, `password_hash`, `role`, `advisory`) values('5','Ralph Antonio Cruz','adviser2@gmail.com','$2y$10$ks046Q3Rr3wXUqYL.W56eurAk6WJ5JJlP9dj5fh.GtRyHlOz50IH6','adviser','11 - B');
insert into `users` (`id`, `name`, `email`, `password_hash`, `role`) values('6','Jessica Mae Lopez','teacher2@gmail.com','$2y$10$ks046Q3Rr3wXUqYL.W56eurAk6WJ5JJlP9dj5fh.GtRyHlOz50IH6','teacher');
