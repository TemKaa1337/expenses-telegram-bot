DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS category_aliases;
DROP TABLE IF EXISTS expenses;
DROP TABLE IF EXISTS exception_logging;
DROP TABLE IF EXISTS log;

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    telegram_id INTEGER,
    first_name VARCHAR,
    second_name VARCHAR
);

CREATE TABLE log (
    id SERIAL PRIMARY KEY,
    chat_id INTEGER,
    request JSON,
    created_at TIMESTAMP
);

CREATE TABLE categories (
    id SERIAL PRIMARY KEY,
    category_name VARCHAR UNIQUE
);

CREATE TABLE category_aliases (
    id SERIAL PRIMARY KEY,
    category_id INTEGER NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE,
    alias VARCHAR
);

CREATE TABLE expenses (
    id SERIAL PRIMARY KEY,
    created_at TIMESTAMP,
    amount NUMERIC(6, 2),
    user_id INTEGER NOT NULL,
    category_id INTEGER NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE,
    note VARCHAR
);

CREATE TABLE exception_logging (
    id SERIAL PRIMARY KEY,
    stack_trace TEXT,
    message TEXT,
    file VARCHAR,
    line INTEGER,
    created_at TIMESTAMP
);

INSERT INTO categories(category_name) values ('Продукты'), ('Кафе'), ('Такси'), ('Steam'), ('Dota2Market'), ('Warzone'), ('Макдоналдс'), ('Одежда'), ('Хостинг и домен'), ('Другое'), ('Компьютерные комплектующие'), ('Шаурма'), ('Хотфикс'), ('Бухич'), ('Коммуналка и тд'), ('Платная стоянка');

INSERT INTO category_aliases(category_id, alias) values (1, 'еда');
INSERT INTO category_aliases(category_id, alias) values (1, 'Еда');
INSERT INTO category_aliases(category_id, alias) values (1, 'продукты');
INSERT INTO category_aliases(category_id, alias) values (2, 'Рестик');
INSERT INTO category_aliases(category_id, alias) values (2, 'рестик');
INSERT INTO category_aliases(category_id, alias) values (2, 'кафе');
INSERT INTO category_aliases(category_id, alias) values (3, 'такси');
INSERT INTO category_aliases(category_id, alias) values (3, 'яндекс');
INSERT INTO category_aliases(category_id, alias) values (3, 'такса');
INSERT INTO category_aliases(category_id, alias) values (4, 'steam');
INSERT INTO category_aliases(category_id, alias) values (4, 'стим');
INSERT INTO category_aliases(category_id, alias) values (4, 'Стим');
INSERT INTO category_aliases(category_id, alias) values (5, 'дотамаркет');
INSERT INTO category_aliases(category_id, alias) values (5, 'дота');
INSERT INTO category_aliases(category_id, alias) values (5, 'dota');
INSERT INTO category_aliases(category_id, alias) values (5, 'Dota');
INSERT INTO category_aliases(category_id, alias) values (6, 'warzone');
INSERT INTO category_aliases(category_id, alias) values (6, 'варзон');
INSERT INTO category_aliases(category_id, alias) values (7, 'мак');
INSERT INTO category_aliases(category_id, alias) values (7, 'Мак');
INSERT INTO category_aliases(category_id, alias) values (7, 'макдак');
INSERT INTO category_aliases(category_id, alias) values (7, 'Макдак');
INSERT INTO category_aliases(category_id, alias) values (7, 'mac');
INSERT INTO category_aliases(category_id, alias) values (7, 'Mac');
INSERT INTO category_aliases(category_id, alias) values (8, 'Вещи');
INSERT INTO category_aliases(category_id, alias) values (8, 'вещи');
INSERT INTO category_aliases(category_id, alias) values (8, 'шмотки');
INSERT INTO category_aliases(category_id, alias) values (8, 'одежда');
INSERT INTO category_aliases(category_id, alias) values (8, 'Одежда');
INSERT INTO category_aliases(category_id, alias) values (9, 'хостинг');
INSERT INTO category_aliases(category_id, alias) values (9, 'Хостинг');
INSERT INTO category_aliases(category_id, alias) values (9, 'домен');
INSERT INTO category_aliases(category_id, alias) values (9, 'Домен');
INSERT INTO category_aliases(category_id, alias) values (10, 'Другое');
INSERT INTO category_aliases(category_id, alias) values (10, 'другое');
INSERT INTO category_aliases(category_id, alias) values (10, 'др');
INSERT INTO category_aliases(category_id, alias) values (11, 'комп');
INSERT INTO category_aliases(category_id, alias) values (11, 'Комп');
INSERT INTO category_aliases(category_id, alias) values (11, 'Компьютер');
INSERT INTO category_aliases(category_id, alias) values (11, 'Компъютер');
INSERT INTO category_aliases(category_id, alias) values (11, 'железо');
INSERT INTO category_aliases(category_id, alias) values (11, 'проц');
INSERT INTO category_aliases(category_id, alias) values (11, 'процессор');
INSERT INTO category_aliases(category_id, alias) values (11, 'оператива');
INSERT INTO category_aliases(category_id, alias) values (12, 'шаурма');
INSERT INTO category_aliases(category_id, alias) values (12, 'Шаурма');
INSERT INTO category_aliases(category_id, alias) values (12, 'шава');
INSERT INTO category_aliases(category_id, alias) values (12, 'шавуха');
INSERT INTO category_aliases(category_id, alias) values (13, 'котфикс');
INSERT INTO category_aliases(category_id, alias) values (13, 'хотфикс');
INSERT INTO category_aliases(category_id, alias) values (14, 'бухич');
INSERT INTO category_aliases(category_id, alias) values (14, 'бухать');
INSERT INTO category_aliases(category_id, alias) values (15, 'вода');
INSERT INTO category_aliases(category_id, alias) values (15, 'Вода');
INSERT INTO category_aliases(category_id, alias) values (15, 'коммуналка');
INSERT INTO category_aliases(category_id, alias) values (15, 'свет');
INSERT INTO category_aliases(category_id, alias) values (15, 'инет');
INSERT INTO category_aliases(category_id, alias) values (15, 'телефон');
INSERT INTO category_aliases(category_id, alias) values (16, 'стоянка');
INSERT INTO category_aliases(category_id, alias) values (16, 'парковка');

