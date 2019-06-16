// configure api db maintainer
GRANT ALL PRIVILEGES ON *.* TO 'user'@'localhost' IDENTIFIED BY '!R3usabl3_Int3rn_Work!';

// create db
CREATE DATABASE machine_base;

// create table
CREATE TABLE IF NOT EXISTS 'machines' (
  'id' CHAR(15) NOT NULL,
  'name' CHAR(15) NOT NULL,
  'requester' CHAR(15) NOT NULL,
  'groups' CHAR(255) NOT NULL,
  'time_requesed' CHAR(30) NOT NULL,
  'isComplete' BOOLEAN NOT NULL,
  'isAccountedFor' BOOLEAN NOT NULL,
  PRIMARY KEY ('id')
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
