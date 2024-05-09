CREATE TABLE IF NOT EXISTS up_special_offer
(
	id    INT         NOT NULL AUTO_INCREMENT,
	title VARCHAR(45) NOT NULL,
	description VARCHAR(255) NOT NULL,
	PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS up_item_special_offer
(
	item_id          INT NOT NULL AUTO_INCREMENT,
	special_offer_id INT NOT NULL,
	PRIMARY KEY (item_id, special_offer_id),
	FOREIGN KEY fk_it (item_id)
		REFERENCES up_item (id)
		ON DELETE RESTRICT
		ON UPDATE RESTRICT,
	FOREIGN KEY fk_so (special_offer_id)
		REFERENCES up_special_offer (id)
		ON DELETE RESTRICT
		ON UPDATE RESTRICT
);


ALTER TABLE up_item
	ADD COLUMN priority INT DEFAULT 0;