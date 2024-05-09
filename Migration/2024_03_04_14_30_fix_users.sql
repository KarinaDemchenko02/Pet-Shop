UPDATE up_users
SET name='Администратор',
    surname='Магазина',
    password='$2y$10$DRf8lLSRmIku1nEN/TON2.Uz0jM7Ov.5VBY0LpmK2WWO.fES.C7WC'
WHERE id = 1;
UPDATE up_users
SET name='Пользователь',
    surname='Обычный',
    password='$2y$10$D73bEWPHJEomPw0tLUN/du6HgXAGr8oJ02Yu7YflxJcZIJGYtcAOS'
WHERE id = 2;