UPDATE up_image t SET t.path = '/images/productImages/2.png' WHERE t.id = 2;

UPDATE up_image t SET t.path = '/images/productImages/4.png' WHERE t.id = 4;

UPDATE up_image t SET t.path = '/images/productImages/1.png' WHERE t.id = 1;

UPDATE up_image t SET t.path = '/images/productImages/3.png' WHERE t.id = 3;

ALTER TABLE up_product MODIFY name VARCHAR(100) NOT NULL;

INSERT INTO up_product (name, description, price)
VALUES ('Переноска для животных', 'Отличная переноска, предназначенная для транспортировки животных! Сочетает в себе функциональность, удобство и яркий дизайн.', 1800),
	   ('Корм Barking Heads для котят', 'Этот потрясающе вкусный рецепт создан из 100% натуральной курицы и рыбы, с использование только самых высококачественных натуральных ингредиентов.', 1900),
	   ('Корм Purina Pro Plan для стерилизованных кошек и кастрированных котов', 'Эксперты PRO PLAN® разработали полнорационный сухой корм для взрослых стерилизованных кошек и кастрированных котов с учетом потребностей Вашего питомца в особой защите мочевыделительной системы. Содержит формулу OPTIRENAL® — сочетание питательных веществ, которые поддерживают здоровье почек.', 1500),
	   ('Витаминное лакомство для собак "для красивой кожи и шерсти"', 'Витаминные лакомства 2U созданы с учетом всех потребностей вашего питомца. Витамины и сбалансированный  минеральный комплекс помогут животному оставаться здоровым и жизнерадостным круглый год.', 120);

INSERT INTO up_product_tag (product_id, tag_id)
VALUES (5, 3),
	   (6, 1),
	   (7, 1),
	   (8, 4);

INSERT INTO up_image (id, path, product_id)
VALUES (5, '/images/productImages/5.png', 5),
	   (6, '/images/productImages/6.png', 6),
	   (7, '/images/productImages/7.png', 7),
	   (8, '/images/productImages/8.png', 8);
