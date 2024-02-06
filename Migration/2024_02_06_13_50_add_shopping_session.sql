CREATE TABLE up_shopping_session
(
	id         INT       NOT NULL AUTO_INCREMENT,
	user_id    INT       NOT NULL,
	created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (id),
	FOREIGN KEY fk_ssu_user (user_id)
		REFERENCES up_users (id)
		ON DELETE NO ACTION
		ON UPDATE NO ACTION
);


CREATE TABLE up_shopping_session_item
(
	item_id             INT NOT NULL,
	shopping_session_id INT NOT NULL,
	quantities          INT NULL,
	PRIMARY KEY (item_id, shopping_session_id),
	FOREIGN KEY fk_ssi_item (item_id)
		REFERENCES up_item (id)
		ON DELETE NO ACTION
		ON UPDATE NO ACTION,
	FOREIGN KEY fk_ssi_shopping_session (shopping_session_id)
		REFERENCES up_shopping_session (id)
		ON DELETE NO ACTION
		ON UPDATE NO ACTION
);