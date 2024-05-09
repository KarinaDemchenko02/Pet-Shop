CREATE TABLE up_token
(
	jti          VARCHAR(50) NOT NULL
		PRIMARY KEY,
	user_id      INT         NOT NULL,
	expiration   INT         NOT NULL,
	finger_print VARCHAR(50) NULL,
	CONSTRAINT fk_token_user
		FOREIGN KEY (user_id) REFERENCES up_users (id)
);