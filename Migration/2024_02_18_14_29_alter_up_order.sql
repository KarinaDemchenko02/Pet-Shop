ALTER TABLE up_order
	ADD edited_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
		AFTER created_at;