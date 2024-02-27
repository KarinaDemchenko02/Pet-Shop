RENAME TABLE up_item TO up_product;
RENAME TABLE up_item_characteristic TO up_product_characteristic;
RENAME TABLE up_order_item TO up_order_product;
RENAME TABLE up_shopping_session_item TO up_shopping_session_product;
RENAME TABLE up_item_tag TO up_product_tag;
RENAME TABLE up_item_special_offer TO up_product_special_offer;


ALTER TABLE up_product_characteristic
	CHANGE COLUMN item_id product_id int not null;


ALTER TABLE up_product_characteristic
	DROP FOREIGN KEY up_item_characteristic_up_characteristic_id_fk,
	ADD CONSTRAINT up_product_characteristic_up_characteristic_id_fk FOREIGN KEY (characteristic_id) REFERENCES up_characteristic (id);

ALTER TABLE up_product_characteristic
	DROP FOREIGN KEY up_item_characteristic_up_item_id_fk,
	ADD CONSTRAINT up_product_characteristic_up_product_id_fk FOREIGN KEY (product_id) REFERENCES up_product (id);

ALTER TABLE up_product_special_offer
	DROP FOREIGN KEY up_product_special_offer_ibfk_1;

ALTER TABLE up_product_special_offer
	CHANGE COLUMN item_id product_id int not null;


ALTER TABLE up_product_special_offer
	ADD CONSTRAINT up_product_special_offer_up_product_id_fk FOREIGN KEY (product_id) REFERENCES up_product (id);

ALTER TABLE up_order_product
	CHANGE COLUMN item_id product_id int not null;

ALTER TABLE up_order_product
	DROP FOREIGN KEY up_order_product_ibfk_2;

ALTER TABLE up_order_product
	ADD CONSTRAINT up_order_product_up_product_id_fk FOREIGN KEY (product_id) REFERENCES up_product (id);

ALTER TABLE up_shopping_session_product
	CHANGE COLUMN item_id product_id int not null;

ALTER TABLE up_product_tag
	CHANGE COLUMN id_item product_id int not null,
	CHANGE COLUMN id_tag tag_id int not null;

ALTER TABLE up_image
	CHANGE COLUMN item_id product_id int not null;

ALTER TABLE up_tags
	CHANGE COLUMN name title varchar(45) not null;