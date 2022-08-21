<?php declare(strict_types = 1);

namespace App\Messages;

enum ErrorMessage: string
{
    case NoExpensesFoundForGivenPeriod = 'Траты за указанный период не найдены.';
    case NoAliasesFound = 'Для выбранной категории нет алиасов.';
    case IncorrectDateFormat = 'Неправильный формат даты.';
    case UnknownError = 'Случилась неизвестная ошибка, обратитесь к администратору.';
    case UnknownCommand = 'Такой команды не существует или она введена неверно.';
    case InsufficientCommandArguments = 'Недостаточно агрументов для команды.';
    case UnknownBotAction = 'Произошла ошибка. Бот умеет отвечать только на текстовые команды и он не умеет отвечать на изменение уже существующих сообщений.';
    case UnknownCategory = 'Такой категории не существует.';
    case UnknownCategoryAlias = 'Такого алиаса категории не существует.';
    case CategoryAlreadyExist = 'Такая категория уже существует.';
    case CategoryAliasAlreadyExists = 'Такой алиас категории уже существует';
    case NoSuchExpense = 'Такой траты нет.';
}