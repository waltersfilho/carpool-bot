create table Caroneiros(
	id bigserial UNIQUE PRIMARY KEY,
	chat_id varchar(255) NOT NULL,
	user_id int NOT NULL,
	username varchar(128),
	spots varchar(128),
	location varchar(128),
	travel_hour timestamp,
	route bit not null,
	expired bit DEFAULT 0::bit;
);

create table caroneiro_pagamento(
	id bigserial UNIQUE PRIMARY KEY,
	chat_id varchar(255) NOT NULL,
	user_id int NOT NULL,
	picpay bit DEFAULT 0::bit,
	carpool bit DEFAULT 0::bit
);

create table avisos (
    id bigserial UNIQUE PRIMARY KEY,
	chat_id varchar(255) NOT NULL,
	message varchar(255) NOT NULL,
	data timestamp,
	expired bit DEFAULT 0::bit
);

