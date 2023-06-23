<?php declare(strict_types=1);

namespace App\Services\Commands;

use App\Services\Formatters\CommandDescriptionFormatter;

final readonly class StartCommand extends BaseCommand
{
    /**
     * @return string
     */
    public function execute(): string
    {
        $descriptions = [
            'Для того, чтобы добавить трату вводите в формате: {сумма траты} {название или алиас раздела} {примечание}. Например: 14.1 кафе мак дак',
            'Для того, чтобы добавить категорию расходов, введите данные в формате: /add_category {название категории}. Например: /add_category Бензин',
            'Для того, чтобы добавить алиас для категории расходов, введите данные в формате: /add_category_alias {название категории} {алиас категории}. Например: /add_category_alias Бензин бенз',
            'Для того, чтобы просмотреть траты за указанный месяц, введите команду в формате: /month_expenses {мм или мм.гг или мм.гггг}.',
            'Несколько примеров:',
            '/month_expenses (выведет траты за текущий месяц)',
            '/month_expenses 8',
            '/month_expenses 10.21',
            '/month_expenses 10.2021',
            'Для того, чтобы просмотреть траты за указанный день, введите команду в формате: /day_expenses {д или д.мм или д.мм.гг}',
            'Несколько примеров:',
            '/day_expenses (выведет траты за текущий день)',
            '/day_expenses 3',
            '/day_expenses 3.10',
            '/day_expenses 3.10.21',
            '/day_expenses 3.10.2021',
            'Для удаления траты, нажмите на синий текст при выводе расходов, он будет в формате /delete_expense100',
            'Для удаления категории, введите команду в формате /delete_category {название категории}. Например:',
            '/delete_category Бензин',
            'Для удаления алиаса категории, введите команду в формате /delete_category_alias {название категории} {название алиаса}. Например:',
            '/delete_category_alias Бензин бенз'
        ];
        foreach (Command::cases() as $command) {
            $descriptions[] = $command->getDescription();
        }
        return CommandDescriptionFormatter::format($descriptions);
    }
}