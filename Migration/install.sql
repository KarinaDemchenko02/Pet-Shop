USE module2;
CREATE TABLE IF NOT EXISTS up_item
(
	id          INT            NOT NULL AUTO_INCREMENT,
	name        VARCHAR(45)    NOT NULL,
	description TEXT           NULL,
	price       DECIMAL(10, 2) NOT NULL,
	added_at    TIMESTAMP      NULL     DEFAULT CURRENT_TIMESTAMP,
	edited_at   TIMESTAMP      NULL     DEFAULT CURRENT_TIMESTAMP,
	is_active   TINYINT(1)     NOT NULL DEFAULT 1,
	PRIMARY KEY (id)
);


CREATE TABLE IF NOT EXISTS up_tags
(
	id   INT         NOT NULL AUTO_INCREMENT,
	name VARCHAR(45) NOT NULL,
	PRIMARY KEY (id)
);


CREATE TABLE IF NOT EXISTS up_item_tag
(
	id_item INT NOT NULL,
	id_tag  INT NOT NULL,
	PRIMARY KEY (id_item, id_tag),
	FOREIGN KEY fk_it_item (id_item)
		REFERENCES up_item (id)
		ON DELETE RESTRICT
		ON UPDATE RESTRICT,
	FOREIGN KEY fk_it_tag (id_tag)
		REFERENCES up_tags (id)
		ON DELETE RESTRICT
		ON UPDATE RESTRICT
);

CREATE TABLE IF NOT EXISTS up_status
(
	id    INT         NOT NULL AUTO_INCREMENT,
	title VARCHAR(45) NOT NULL,
	PRIMARY KEY (id)
);


CREATE TABLE IF NOT EXISTS up_role
(
	id    INT         NOT NULL AUTO_INCREMENT,
	title VARCHAR(45) NOT NULL,
	PRIMARY KEY (id)
);


CREATE TABLE IF NOT EXISTS up_users
(
	id       INT         NOT NULL AUTO_INCREMENT,
	email    VARCHAR(20) NULL,
	password VARCHAR(40) NOT NULL,
	role_id  INT         NOT NULL,
	tel      VARCHAR(45) NOT NULL,
	name     VARCHAR(45) NULL,
	PRIMARY KEY (id),
	FOREIGN KEY fk_users_role (role_id)
		REFERENCES up_role (id)
		ON DELETE RESTRICT
		ON UPDATE RESTRICT
);



CREATE TABLE IF NOT EXISTS up_order
(
	id               INT         NOT NULL AUTO_INCREMENT,
	user_id          INT         NOT NULL,
	delivery_address VARCHAR(45) NOT NULL,
	status_id        INT         NOT NULL,
	created_at       TIMESTAMP   NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (id),
	FOREIGN KEY fk_order_status (status_id)
		REFERENCES up_status (id)
		ON DELETE RESTRICT
		ON UPDATE RESTRICT,
	FOREIGN KEY fk_order_user (user_id)
		REFERENCES up_users (id)
		ON DELETE RESTRICT
		ON UPDATE RESTRICT
);


CREATE TABLE IF NOT EXISTS up_order_item
(
	order_id   INT         NOT NULL,
	item_id    INT         NOT NULL,
	quantities INT         NOT NULL,
	price      VARCHAR(45) NOT NULL,
	PRIMARY KEY (order_id, item_id),
	CONSTRAINT order_id
		FOREIGN KEY fk_ot_order (order_id)
			REFERENCES up_order (id)
			ON DELETE RESTRICT
			ON UPDATE RESTRICT,
	CONSTRAINT good_id
		FOREIGN KEY fk_ot_item (item_id)
			REFERENCES up_item (id)
			ON DELETE RESTRICT
			ON UPDATE RESTRICT
);


CREATE TABLE IF NOT EXISTS up_image
(
	id      INT         NOT NULL,
	path    VARCHAR(45) NULL,
	item_id INT         NOT NULL,
	PRIMARY KEY (id),
	FOREIGN KEY fk_image_item (item_id)
		REFERENCES up_item (id)
		ON DELETE RESTRICT
		ON UPDATE RESTRICT
);
