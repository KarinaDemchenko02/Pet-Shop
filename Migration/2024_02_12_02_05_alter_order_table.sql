ALTER TABLE up_order
	MODIFY user_id INT;

ALTER TABLE up_order
	ADD name VARCHAR(45) NOT NULL,
	ADD surname VARCHAR(45) NOT NULL;
