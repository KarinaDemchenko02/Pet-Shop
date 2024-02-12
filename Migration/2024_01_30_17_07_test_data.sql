INSERT INTO up_role (id, title)
VALUES (1, 'Администратор'),
	   (2, 'Пользователь'),
	   (3, 'Гость');

INSERT INTO up_status (id, title)
VALUES (1, 'В ожидании'),
       (2, 'В обработке'),
       (3, 'Отправлено'),
       (4, 'Доставлено');

INSERT INTO up_tags (name)
VALUES ('Корм для животных'),
       ('Игрушки для питомцев'),
       ('Аксессуары для домашних животных'),
       ('Ветеринарные препараты');

INSERT INTO up_item (name, description, price)
VALUES ('Сухой корм для собак', 'Питательный корм для здоровья вашего питомца', 1200.50),
       ('Мяч для кошек', 'Яркая игрушка для активных кошек', 19.99),
       ('Когтеточка для котов', 'Деревянная когтеточка с игрушкой', 29.99),
       ('Витаминный комплекс для птиц', 'Полезные витамины для вашего птичьего друга', 49.99);

INSERT INTO up_users (email, password, role_id, tel, name)
VALUES ('admin@example.com', 'admin_password', 1, '+71234567890', 'Администратор Магазина'),
       ('user@example.com', 'user_password', 2, '+79876543210', 'Обычный Пользователь');

INSERT INTO up_order (user_id, delivery_address, status_id, name, surname)
VALUES (19, '123 Улица Главная, Москва', 1, 'Антон', 'Антонов'),
       (20, '456 Улица Дубравная, Санкт-Петербург', 2, 'Иван', 'Иванов');

INSERT INTO up_item_tag (id_item, id_tag)
VALUES (8, 5),
       (9, 6),
       (10, 7),
       (11, 8);

INSERT INTO up_order_item (order_id, item_id, quantities, price)
VALUES (45, 8, 2, '1200.50'),
       (46, 9, 3, '19.99'),
       (45, 11, 1, '49.99');

INSERT INTO up_image (id, path, item_id)
VALUES (1, '/images/productImages/1.jpg', 8),
       (2, '/images/productImages/2.jpg', 9),
       (3, '/images/productImages/3.jpg', 10),
       (4, '/images/productImages/4.jpg', 11);
