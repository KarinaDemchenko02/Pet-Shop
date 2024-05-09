UPDATE up_order SET name='Администратор', surname='Магазина' WHERE id=1;
UPDATE up_order SET name='Пользователь', surname='Обычный' WHERE id=2;

UPDATE up_users SET password='$2y$10$YUyZE4wLuWaEB6rxGkQKYusMhzYqc/.MseftICZCw29k4SqWL40ta' WHERE id=1;
UPDATE up_users SET password='$2y$10$xVHYD6KCPyiTjIOCJEMvYOyRK1EhU5SatrGCFedPZXFRi6ZaSvH5S' WHERE id=2;

INSERT INTO up_shopping_session (id, user_id) VALUES (1, 2);
INSERT INTO up_shopping_session_product (product_id, shopping_session_id, quantities) VALUES (1, 1, 1)