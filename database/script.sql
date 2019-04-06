create table Caroneiros(
	id bigserial UNIQUE PRIMARY KEY,
	chat_id int NOT NULL,
	user_id int NOT NULL,
	username varchar(128),
	spots varchar(128),
	location varchar(128),
	travel_hour time,
	route bit not null,
	expiration timestamp
);
