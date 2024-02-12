INSERT INTO up_role (title)
VALUES ('Администратор'),
       ('Пользователь'),
       ('Гость');

INSERT INTO up_status (title)
VALUES ('В ожидании'),
       ('В обработке'),
       ('Отправлено'),
       ('Доставлено');

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

INSERT INTO up_order (user_id, delivery_address, status_id)
VALUES (1, '123 Улица Главная, Москва', 1),
       (2, '456 Улица Дубравная, Санкт-Петербург', 2);

INSERT INTO up_item_tag (id_item, id_tag)
VALUES (1, 1),
       (2, 2),
       (3, 3),
       (4, 4);

INSERT INTO up_order_item (order_id, item_id, quantities, price)
VALUES (1, 1, 2, '1200.50'),
       (2, 2, 3, '19.99'),
       (1, 4, 1, '49.99');

INSERT INTO up_image (id, path, item_id)
VALUES (1, '/images/productImages/productImages.png', 1),
       (2, '/images/productImages/productImages.png', 2),
       (3, '/images/productImages/productImages.png', 3),
       (4, '/images/productImages/productImages.png', 4);
