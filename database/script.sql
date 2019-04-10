create table Caroneiros(
	id bigserial UNIQUE PRIMARY KEY,
	chat_id varchar(255) NOT NULL,
	user_id int NOT NULL,
	username varchar(128),
	spots varchar(128),
	location varchar(128),
	travel_hour timestamp,
	route bit not null
);

create table caroneiro_pagamento(
	id bigserial UNIQUE PRIMARY KEY,
	chat_id varchar(255) NOT NULL,
	user_id int NOT NULL,
	picpay bit DEFAULT 0::bit,
	wunder bit DEFAULT 0::bit
);
