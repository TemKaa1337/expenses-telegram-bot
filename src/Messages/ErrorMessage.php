<?php declare(strict_types = 1);

namespace App\Messages;

enum ErrorMessage: string
{
    case NoExpensesFoundForGivenPeriod = 'Траты за указанный период не найдены.';
    case IncorrectDateFormat = 'Неправильный формат даты.';
    case UnknownError = 'Случилась неизвестная ошибка, обратитесь к администратору.';
    case UnknownCommand = 'Такой команды не существует или она введена неверно.';
    case InsufficientCommandArguments = 'Недостаточно агрументов для команды.';
    case UnknownBotAction = 'Произошла ошибка. Бот умеет отвечать только на текстовые команды.';
    case UnknownCategory = 'Такой категории не существует.';
    case CategoryAlreadyExist = 'Такая категория уже существует.';
}