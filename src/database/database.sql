DROP DATABASE IF EXISTS finance_bot;

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    telegram_id INTEGER,
    first_name VARCHAR,
    second_name VARCHAR,
    last_latitude NUMERIC(10, 8),
    last_longitude NUMERIC(10, 8)
);

CREATE TABLE categories (
    id SERIAL PRIMARY KEY,
    category_name VARCHAR
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
