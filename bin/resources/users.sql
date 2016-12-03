DROP TABLE IF EXISTS users;

CREATE TABLE users(
    _id integer,
    name TEXT,
    CONSTRAINT users_pk  PRIMARY KEY (_id)
);

INSERT INTO users VALUES(1,'Shrabani');
INSERT INTO users VALUES(3,'Mitu');
INSERT INTO users VALUES(2,'Salma');
INSERT INTO users VALUES(4,'Shubhra');
INSERT INTO users VALUES(8,'Fazjul');
INSERT INTO users VALUES(7,'Ripon');
INSERT INTO users VALUES(6,'Firoza');
INSERT INTO users VALUES(5,'Papia');
INSERT INTO users VALUES(0,'admin');
INSERT INTO users VALUES(9,'Kumar');
INSERT INTO users VALUES(10,'Utpal');
INSERT INTO users VALUES(11,'Shakil');
INSERT INTO users VALUES(12,'Ronak');

