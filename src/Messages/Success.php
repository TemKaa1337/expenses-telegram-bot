<?php declare(strict_types = 1);

namespace App\Messages;

enum Success: string
{
    case ExpenseAdded = 'Новая трата добавлена успешно!';
    case ExpenseDeleted = 'Трата успешно удалена.';
    case CategoryAdded = 'Новая категория успешно добавлена!';
    case CategoryAliasAdded = 'Алиас для категории успешно добавлен!';
    case CategoryDeleted = 'Категория успешно удалена!';
    case CategoryAliasDeleted = 'Алиас категории успешно удален!';
}