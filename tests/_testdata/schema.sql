DROP TABLE IF EXISTS "users";
CREATE TABLE "users" (
             "user_id" INTEGER PRIMARY KEY  AUTOINCREMENT NOT NULL UNIQUE, 
             "first_name" VARCHAR DEFAULT Jane, 
             "last_name" VARCHAR NOT NULL DEFAULT 'Doe Doe', 
             "created" DATETIME NOT NULL, 
             "owner" INTEGER,
             "latitude" FLOAT,
             "longitude" FLOAT);
DROP TABLE IF EXISTS "user_mails";
CREATE TABLE "user_mails" (
             "user_id" INTEGER NOT NULL, 
             "mail_id" INTEGER NOT NULL);
DROP TABLE IF EXISTS "mails";
CREATE TABLE "mails" (
             "user_id" INTEGER NOT NULL, 
             "mail_id" INTEGER NOT NULL,
             "mail" VARCHAR NOT NULL);
             
DROP TABLE IF EXISTS "tick_collection";
CREATE TABLE "tick_collection" (
             "tick_collection_id" INTEGER PRIMARY KEY  AUTOINCREMENT NOT NULL UNIQUE,
             "color" VARCHAR,
             "latitude" FLOAT,
             "longitude" FLOAT);
             
DROP TABLE IF EXISTS "tick_collection2";
CREATE TABLE "tick_collection2" (
             "tick_collection_id" INTEGER PRIMARY KEY  AUTOINCREMENT NOT NULL UNIQUE,
             "color" VARCHAR,
             "latitude" FLOAT,
             "longitude" FLOAT);