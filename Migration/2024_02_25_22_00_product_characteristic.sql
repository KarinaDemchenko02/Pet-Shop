create table up_characteristic
(
	id    int auto_increment
		primary key,
	title varchar(255) not null
);

create table up_item_characteristic
(
	item_id           int          not null,
	characteristic_id int          not null,
	value             varchar(255) not null,
	primary key (item_id, characteristic_id),
	constraint up_item_characteristic_up_characteristic_id_fk
		foreign key (characteristic_id) references up_characteristic (id),
	constraint up_item_characteristic_up_item_id_fk
		foreign key (item_id) references up_item (id)
);

INSERT INTO eshop.up_characteristic (id, title) VALUES (1, 'страна производитель');
INSERT INTO eshop.up_characteristic (id, title) VALUES (2, 'материал');

INSERT INTO eshop.up_item_characteristic (item_id, characteristic_id, value) VALUES (1, 1, 'Россия');
INSERT INTO eshop.up_item_characteristic (item_id, characteristic_id, value) VALUES (1, 2, 'Дерево');
INSERT INTO eshop.up_item_characteristic (item_id, characteristic_id, value) VALUES (2, 1, 'Испания');
