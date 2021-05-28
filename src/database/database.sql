DROP DATABASE IF EXISTS finance_bot;

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    telegram_id INTEGER,
    first_name VARCHAR,
    second_name VARCHAR
);

CREATE TABLE categories (
    id SERIAL PRIMARY KEY,
    category_name VARCHAR UNIQUE
);

CREATE TABLE categories_aliases (
    id SERIAL PRIMARY KEY,
    FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE,
    alias VARCHAR
);

CREATE TABLE expenses (
    id SERIAL PRIMARY KEY,
    created_at TIMESTAMP,
    amount NUMERIC(6, 2),
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

INSERT INTO categories(category_name) values ('Еда'), ('Квартира'), ('Кафе'), ('Развлечения'), ('Еда'), ('Еда');
