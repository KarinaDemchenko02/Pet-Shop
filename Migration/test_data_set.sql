-- Заполнение таблицы up_item
INSERT INTO up_item (name, description, price) VALUES ('Тестовый товар 1', 'Описание тестового товара 1', 50.00);
INSERT INTO up_item (name, description, price) VALUES ('Тестовый товар 2', 'Описание тестового товара 2', 70.00);
INSERT INTO up_item (name, description, price) VALUES ('Тестовый товар 3', 'Описание тестового товара 3', 100.00);

-- Заполнение таблицы up_tags
INSERT INTO up_tags (name) VALUES ('Тэг 1');
INSERT INTO up_tags (name) VALUES ('Тэг 2');
INSERT INTO up_tags (name) VALUES ('Тэг 3');

-- Заполнение таблицы up_status
INSERT INTO up_status (title) VALUES ('Статус 1');
INSERT INTO up_status (title) VALUES ('Статус 2');
INSERT INTO up_status (title) VALUES ('Статус 3');

-- Заполнение таблицы up_role
INSERT INTO up_role (title) VALUES ('Роль 1');
INSERT INTO up_role (title) VALUES ('Роль 2');
INSERT INTO up_role (title) VALUES ('');

-- Заполнение таблицы up_users
INSERT INTO up_users (email, password, role_id, tel, name) VALUES ('test1@email.com', 'password1', 1, '123456789', 'Пользователь 1');
INSERT INTO up_users (email, password, role_id, tel) VALUES ('test2@email.com', 'password2', 2, '987654321');


-- Заполнение таблицы up_order
INSERT INTO up_order (user_id, delivery_address, status_id) VALUES (1, 'Адрес доставки 1', 1);
INSERT INTO up_order (user_id, delivery_address, status_id) VALUES (2, 'Адрес доставки 2', 2);

-- Заполнение таблицы up_order_item
INSERT INTO up_order_item (order_id, item_id, quantities, price) VALUES (1, 1, 2, '100.00');
INSERT INTO up_order_item (order_id, item_id, quantities, price) VALUES (1, 2, 1, '70.00');
INSERT INTO up_order_item (order_id, item_id, quantities, price) VALUES (2, 3, 3, '300.00');

-- Заполнение таблицы up_image
INSERT INTO up_image (id, path, item_id) VALUES (1, 'путь_к_изображению_1', 1);
INSERT INTO up_image (id, path, item_id) VALUES (2, 'путь_к_изображению_2', 2);

INSERT INTO eshop.up_item_tag (id_item, id_tag) VALUES (1, 1);
INSERT INTO eshop.up_item_tag (id_item, id_tag) VALUES (2, 1);
INSERT INTO eshop.up_item_tag (id_item, id_tag) VALUES (2, 2);
INSERT INTO eshop.up_item_tag (id_item, id_tag) VALUES (3, 1);
INSERT INTO eshop.up_item_tag (id_item, id_tag) VALUES (3, 2);
INSERT INTO eshop.up_item_tag (id_item, id_tag) VALUES (3, 3);