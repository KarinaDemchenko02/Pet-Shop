CREATE TABLE IF NOT EXISTS up_special_offer
(
	id          INT            NOT NULL AUTO_INCREMENT,
	title        VARCHAR(45)    NOT NULL,
	PRIMARY KEY (id)
	);

ALTER TABLE up_item ADD COLUMN special_offer_id INT;

ALTER TABLE up_item ADD
	FOREIGN KEY fk_it_so (special_offer_id)
	REFERENCES up_special_offer (id)
	ON DELETE RESTRICT
	ON UPDATE RESTRICT;

ALTER TABLE up_item ADD COLUMN priority INT DEFAULT 0;