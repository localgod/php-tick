INSERT INTO "users" (first_name,last_name,created,owner,latitude,longitude) VALUES('John','Doe','2011-01-01 00:00:00',1,43.8801,-75.6839);
INSERT INTO "users" (first_name,last_name,created,owner,latitude,longitude) VALUES('Jonny','Doe','2011-02-01 00:00:00',1,43.8801,-75.6839);
INSERT INTO "users" (first_name,last_name,created,owner,latitude,longitude) VALUES('Jacob','Doe','2011-03-01 00:00:00',1,43.8801,-75.6839);
INSERT INTO "user_mails" (user_id,mail_id) VALUES(1,1);
INSERT INTO "user_mails" (user_id,mail_id) VALUES(1,2);
INSERT INTO "mails" (user_id,mail_id,mail) VALUES(1,1,'Jonny');
INSERT INTO "mails" (user_id,mail_id,mail) VALUES(1,2,'Jacob');