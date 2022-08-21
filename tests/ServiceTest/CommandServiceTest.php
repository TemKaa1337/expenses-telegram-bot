<?php declare(strict_types=1);

use App\Command\Command;
use PHPUnit\Framework\TestCase;
use App\Database\SqlDatabase;
use App\Exception\CategoryAlreadyExistException;
use App\Exception\InvalidInputException;
use App\Exception\NoCategoriesFoundException;
use App\Exception\NoCategoryAliasesFoundException;
use App\Exception\NoExpenseFoundException;
use App\Exception\NoSuchCategoryAliasException;
use App\Exception\NoSuchCategoryException;
use App\Messages\SuccessMessage;
use App\Services\CommandService;
use App\Services\Validator\Date\DateValidator;
use App\Services\Validator\Date\MonthAndYearValidator;

final class CommandServiceTest extends TestCase
{
    public function testStartCommand(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    []
                ));

        $command = Command::Start;

        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: []);
        $result = $commandService->handle();

        $commands = Command::cases();
        $output = [];

        foreach ($commands as $command) {
            $description = $command->getDescription();
            $output[] = "{$command->value} {$description}";
        }

        $output = array_merge(
            $output,
            [
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
            ]
        );

        $this->assertEquals(implode(PHP_EOL, $output), $result);
    }

    public function testAddExpenseCommandWithNote(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [['category_id' => 1]],
                    [['category_name' => 'test']],
                    [['id' => 1]],
                    []
                ));

        $command = Command::AddExpense;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: [1, 'category_name', 'note']);
        $result = $commandService->handle();
        
        $this->assertEquals($result, SuccessMessage::ExpenseAdded->value);
    }

    public function testAddExpenseCommandWithoutNote(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [['category_id' => 1]],
                    [['category_name' => 'test']],
                    [['id' => 1]],
                    []
                ));

        $command = Command::AddExpense;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: [1, 'category_name']);
        $result = $commandService->handle();
        
        $this->assertEquals($result, SuccessMessage::ExpenseAdded->value);
    }

    public function testDayExpensesWithEmptyDate(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $currentDatetime = date('Y-m-d H:i:s');
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [['id' => 1, 'created_at' => $currentDatetime, 'amount' => 5, 'category_name' => 'test', 'note' => 'this_is_a_note']],
                    []
                ));

        $command = Command::DayExpenses;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: []);
        $result = $commandService->handle();

        $toCompare = [explode(' ', $currentDatetime)[1].' (/delete_expense1) - 5р, test, this_is_a_note.', 'Итого: 5р.'];

        $this->assertEquals($result, implode(PHP_EOL, $toCompare));
    }

    public function testDayExpensesWithOnlyDay(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $currentDatetime = date('Y-m-d H:i:s');
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [['id' => 1, 'created_at' => $currentDatetime, 'amount' => 5, 'category_name' => 'test', 'note' => 'this_is_a_note']],
                    []
                ));

        $command = Command::DayExpenses;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: [date('d')]);
        $result = $commandService->handle();

        $toCompare = [explode(' ', $currentDatetime)[1].' (/delete_expense1) - 5р, test, this_is_a_note.', 'Итого: 5р.'];

        $this->assertEquals($result, implode(PHP_EOL, $toCompare));
    }

    public function testDayExpensesWithDayAndMonth(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $currentDatetime = date('Y-m-d H:i:s');
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [['id' => 1, 'created_at' => $currentDatetime, 'amount' => 5, 'category_name' => 'test', 'note' => 'this_is_a_note']],
                    []
                ));

        $command = Command::DayExpenses;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: [date('d.m')]);
        $result = $commandService->handle();

        $toCompare = [explode(' ', $currentDatetime)[1].' (/delete_expense1) - 5р, test, this_is_a_note.', 'Итого: 5р.'];

        $this->assertEquals($result, implode(PHP_EOL, $toCompare));
    }

    public function testDayExpensesWithFullDate(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $currentDatetime = date('Y-m-d H:i:s');
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [['id' => 1, 'created_at' => $currentDatetime, 'amount' => 5, 'category_name' => 'test', 'note' => 'this_is_a_note']],
                    []
                ));

        $command = Command::DayExpenses;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: [date('d.m.Y')]);
        $result = $commandService->handle();

        $toCompare = [explode(' ', $currentDatetime)[1].' (/delete_expense1) - 5р, test, this_is_a_note.', 'Итого: 5р.'];

        $this->assertEquals($result, implode(PHP_EOL, $toCompare));
    }

    public function testMonthExpensesWithEmptyDate(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $currentDatetime = date('Y-m-d H:i:s');
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [['id' => 1, 'created_at' => $currentDatetime, 'amount' => 5, 'category_name' => 'test', 'note' => 'this_is_a_note']],
                    []
                ));

        $command = Command::MonthExpenses;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: []);
        $result = $commandService->handle();

        $dateValidator = new MonthAndYearValidator(date: '', allowEmptyDate: true);
        $dateInfo = $dateValidator->validate();

        $daysPassedUntillNow = round((time() - strtotime($dateInfo['startDate'].' 00:00:00')) / 60 / 60 / 24);
        $avg = number_format(5 / $daysPassedUntillNow, 2);

        $toCompare = [date('d.m.Y H:i:s', strtotime($currentDatetime)).' (/delete_expense1) - 5р, test, this_is_a_note.', 'Итого: '.$avg.'р. в среднем за день.', 'Всего: 5р.'];

        $this->assertEquals($result, implode(PHP_EOL, $toCompare));
    }

    public function testMonthExpensesWithGivenMonth(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $currentDatetime = date('Y-m-d H:i:s');
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [['id' => 1, 'created_at' => $currentDatetime, 'amount' => 5, 'category_name' => 'test', 'note' => 'this_is_a_note']],
                    []
                ));

        $command = Command::MonthExpenses;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: ['4']);
        $result = $commandService->handle();

        $dateValidator = new MonthAndYearValidator(date: '4', allowEmptyDate: true);
        $dateInfo = $dateValidator->validate();

        $daysPassedUntillNow = round((time() - strtotime($dateInfo['startDate'].' 00:00:00')) / 60 / 60 / 24);
        $avg = number_format(5 / $daysPassedUntillNow, 2);

        $toCompare = [date('d.m.Y H:i:s', strtotime($currentDatetime)).' (/delete_expense1) - 5р, test, this_is_a_note.', 'Итого: '.$avg.'р. в среднем за день.', 'Всего: 5р.'];

        $this->assertEquals($result, implode(PHP_EOL, $toCompare));
    }

    public function testMonthExpensesWithGivenMonthAndYear(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $currentDatetime = date('Y-m-d H:i:s');
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [['id' => 1, 'created_at' => $currentDatetime, 'amount' => 5, 'category_name' => 'test', 'note' => 'this_is_a_note']],
                    []
                ));

        $command = Command::MonthExpenses;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: ['4.21']);
        $result = $commandService->handle();

        $dateValidator = new MonthAndYearValidator(date: '4.21', allowEmptyDate: true);
        $dateInfo = $dateValidator->validate();

        $daysPassedUntillNow = round((time() - strtotime($dateInfo['startDate'].' 00:00:00')) / 60 / 60 / 24);
        $avg = number_format(5 / $daysPassedUntillNow, 2);

        $toCompare = [date('d.m.Y H:i:s', strtotime($currentDatetime)).' (/delete_expense1) - 5р, test, this_is_a_note.', 'Итого: '.$avg.'р. в среднем за день.', 'Всего: 5р.'];

        $this->assertEquals($result, implode(PHP_EOL, $toCompare));
    }

    public function testDeleteExpenseCommandWithExistingExpense(): void
    {
        $currentDatetime = date('Y-m-d H:i:s');
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [['id' => 1, 'created_at' => $currentDatetime, 'amount' => 5, 'category_name' => 'test', 'note' => 'this_is_a_note']],
                    []
                ));

        $command = Command::DeleteExpense;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: ['5']);
        $result = $commandService->handle();

        $this->assertEquals($result, SuccessMessage::ExpenseDeleted->value);
    }

    public function testDeleteExpenseCommandWithoutExistingExpense(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([]);

        $command = Command::DeleteExpense;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: ['5']);
        $this->expectException(NoExpenseFoundException::class);
        $commandService->handle();
    }

    public function testAllAliasesListWithExistingCategories(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn(
                    [
                        ['id' => 1, 'category_name' => 'test1', 'user_id' => 1, 'alias' => 'alias1'],
                        ['id' => 2, 'category_name' => 'test1', 'user_id' => 1, 'alias' => 'alias2'],
                        ['id' => 3, 'category_name' => 'test2', 'user_id' => 1, 'alias' => 'alias3']
                    ]
                );

        $command = Command::AllAliases;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: ['5']);
        $result = $commandService->handle();

        $output = ['Список алиасов для категорий:', 'test1:', ' - alias1', ' - alias2', 'test2:', ' - alias3'];
        $this->assertEquals($result, implode(PHP_EOL, $output));
    }

    public function testAllAliasesListWithoutExistingCategories(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([]);

        $command = Command::AllAliases;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: ['5']);
        $this->expectException(NoCategoriesFoundException::class);
        $commandService->handle();
    }

    public function testSpecificAliasesListWithExistingCategories(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [[
                        'id' => 1
                    ]],
                    [[
                        'id' => 1
                    ]],
                    [
                        ['id' => 1, 'category_name' => 'test1', 'user_id' => 1, 'alias' => 'alias1'],
                        ['id' => 2, 'category_name' => 'test1', 'user_id' => 1, 'alias' => 'alias2']
                    ]
                ));

        $command = Command::SpecificAliases;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: ['test1']);
        $result = $commandService->handle();

        $output = ['Список алиасов для категории test1:', ' - alias1', ' - alias2'];
        $this->assertEquals($result, implode(PHP_EOL, $output));
    }

    public function testSpecificAliasesListWithoutExistingCategories(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([]);

        $command = Command::SpecificAliases;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: ['unused']);
        $this->expectException(NoSuchCategoryException::class);
        $commandService->handle();
    }

    public function testSpecificAliasesListWithoutExistingCategoryAliaseses(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [[
                        'id' => 1
                    ]],
                    [],
                    []
                ));

        $command = Command::SpecificAliases;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: ['unused']);
        $this->expectException(NoCategoryAliasesFoundException::class);
        $commandService->handle();
    }

    public function testAddCategoryWithExistingCategory(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [[
                        'id' => 1
                    ]]
                ));

        $command = Command::AddCategory;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: ['new_category']);
        $this->expectException(CategoryAlreadyExistException::class);
        $commandService->handle();
    }

    public function testAddCategoryWithoutExistingCategory(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [],
                    [],
                    [['id' => 1]],
                    [],
                    []
                ));

        $command = Command::AddCategory;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: ['new_category']);
        $result = $commandService->handle();
        $this->assertEquals($result, SuccessMessage::CategoryAdded->value);
    }

    public function addCategoryAliasWhenCategoryDoesntExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([]);

        $command = Command::AddCategoryAlias;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: ['new_category', 'new_alias']);
        $this->expectException(NoSuchCategoryException::class);
        $commandService->handle();
    }

    public function addCategoryAliasWhenCategoryExistAndAliasDoesntExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [['id' => 1]],
                    [],
                    []
                ));

        $command = Command::AddCategoryAlias;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: ['new_category', 'new_alias']);
        $result = $commandService->handle();
        $this->assertEquals($result, SuccessMessage::CategoryAliasAdded->value);
    }

    public function addCategoryAliasWhenCategoryExistAndAliasAlreadyExists(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [['id' => 1]],
                    [['id' => 1]]
                ));

        $command = Command::AddCategoryAlias;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: ['new_category', 'new_alias']);
        $this->expectException(CategoryAliasAlreadyExistException::class);
        $commandService->handle();
    }

    public function testDeleteCategoryWhenCategoryExists(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [['id' => 1]],
                    [],
                    []
                ));

        $command = Command::DeleteCategory;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: ['new_category']);
        $result = $commandService->handle();
        $this->assertEquals($result, SuccessMessage::CategoryDeleted->value);
    }

    public function testDeleteCategoryWhenCategoryDoesntExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([]);

        $command = Command::DeleteCategory;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: ['new_category']);
        $this->expectException(NoSuchCategoryException::class);
        $commandService->handle();
    }

    public function testDeleteCategoryAliasWhenCategoryDoesntExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([]);
                    
        $command = Command::DeleteCategoryAlias;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: ['new_category', 'alias']);
        $this->expectException(NoSuchCategoryException::class);
        $commandService->handle();
    }

    public function testDeleteCategoryAliasWhenCategoryExistAndAliasDoesntExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [['id' => 1]],
                    []
                ));

        $command = Command::DeleteCategoryAlias;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: ['new_category', 'alias']);
        $this->expectException(NoSuchCategoryAliasException::class);
        $commandService->handle();
    }

    public function testDeleteCategoryAliasWhenCategoryAndAliasExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [['id' => 1]],
                    [['id' => 2]],
                    []
                ));
                    
        $command = Command::DeleteCategoryAlias;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: ['new_category', 'alias']);
        $result = $commandService->handle();
        $this->assertEquals($result, SuccessMessage::CategoryAliasDeleted->value);
    }

    public function testMonthExpensesByCategoryWhenExpensesDontExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    []
                ));
                    
        $command = Command::MonthExpensesByCategory;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: []);
        $this->expectException(NoExpenseFoundException::class);
        $commandService->handle();
    }

    public function testMonthExpensesByCategoryWithEmptyDate(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [
                        ['id' => 1, 'category_name' => 'category1', 'amount' => 5.5],
                        ['id' => 2, 'category_name' => 'category1', 'amount' => 1.5],
                        ['id' => 3, 'category_name' => 'category2', 'amount' => 2],
                        ['id' => 4, 'category_name' => 'category2', 'amount' => 12]
                    ]
                ));
                    
        $command = Command::MonthExpensesByCategory;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: []);
        $result = $commandService->handle();

        $output = ['category1: 7р.', 'category2: 14р.', 'Итого: 21р.'];
        $this->assertEquals($result, implode(PHP_EOL, $output));
    }

    public function testMonthExpensesByCategoryWithMonth(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [
                        ['id' => 1, 'category_name' => 'category1', 'amount' => 5.5],
                        ['id' => 2, 'category_name' => 'category1', 'amount' => 1.5],
                        ['id' => 3, 'category_name' => 'category2', 'amount' => 2],
                        ['id' => 4, 'category_name' => 'category2', 'amount' => 12]
                    ]
                ));
                    
        $command = Command::MonthExpensesByCategory;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: ['07']);
        $result = $commandService->handle();

        $output = ['category1: 7р.', 'category2: 14р.', 'Итого: 21р.'];
        $this->assertEquals($result, implode(PHP_EOL, $output));
    }

    public function testMonthExpensesByCategoryWithMonthAndYear(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [
                        ['id' => 1, 'category_name' => 'category1', 'amount' => 5.5],
                        ['id' => 2, 'category_name' => 'category1', 'amount' => 1.5],
                        ['id' => 3, 'category_name' => 'category2', 'amount' => 2],
                        ['id' => 4, 'category_name' => 'category2', 'amount' => 12]
                    ]
                ));
                    
        $command = Command::MonthExpensesByCategory;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: ['07.22']);
        $result = $commandService->handle();

        $output = ['category1: 7р.', 'category2: 14р.', 'Итого: 21р.'];
        $this->assertEquals($result, implode(PHP_EOL, $output));
    }

    public function testAverageEachMonthExpensesWhenExpensesDontExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([]);
                    
        $command = Command::AverageEachMonthExpenses;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: []);
        $this->expectException(NoExpenseFoundException::class);
        $commandService->handle();
    }

    public function testAverageEachMonthExpensesWhenExpensesExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [
                        ['id' => 4, 'month' => '10', 'year' => '2021', 'category_name' => 'category3', 'sum' => 12],
                        ['id' => 3, 'month' => '09', 'year' => '2021', 'category_name' => 'category2', 'sum' => 2],
                        ['id' => 2, 'month' => '08', 'year' => '2021', 'category_name' => 'category1', 'sum' => 1.5],
                        ['id' => 1, 'month' => '07', 'year' => '2021', 'category_name' => 'category1', 'sum' => 5.5]
                    ]
                ));
                    
        $command = Command::AverageEachMonthExpenses;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: ['07.22']);
        $result = $commandService->handle();

        $output = ['category3: 12р. | 0р. | 0р. | 0р.', 'category2: 0р. | 2р. | 0р. | 0р.', 'category1: 0р. | 0р. | 1.5р. | 5.5р.'];

        $this->assertEquals($result, implode(PHP_EOL, $output));
    }

    public function testTotalMonthExpensesWhenExpensesDontExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([]);
                    
        $command = Command::TotalMonthExpenses;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: []);
        $this->expectException(NoExpenseFoundException::class);
        $commandService->handle();
    }

    public function testTotalMonthExpensesWhenExpensesExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [
                        ['id' => 4, 'month' => '10', 'year' => '2021', 'category_name' => 'category3', 'sum' => 12],
                        ['id' => 3, 'month' => '09', 'year' => '2021', 'category_name' => 'category2', 'sum' => 2],
                        ['id' => 2, 'month' => '07', 'year' => '2021', 'category_name' => 'category1', 'sum' => 7]
                    ]
                ));
                    
        $command = Command::TotalMonthExpenses;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: ['07.22']);
        $result = $commandService->handle();

        $output = ['10.2021 - 12р.', '09.2021 - 2р.', '07.2021 - 7р.'];

        $this->assertEquals($result, implode(PHP_EOL, $output));
    }

    public function testExpensesFromDattimeWhenExpensesDontExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([]);
                    
        $command = Command::ExpensesFromDatetime;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: [date('d.m.Y', strtotime('-1 day'))]);
        $this->expectException(NoExpenseFoundException::class);
        $commandService->handle();
    }

    public function testExpensesFromDatetimeWhenDateIsEmpty(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [
                        ['id' => 4, 'month' => '10', 'year' => '2021', 'category_name' => 'category3', 'sum' => 12],
                        ['id' => 3, 'month' => '09', 'year' => '2021', 'category_name' => 'category2', 'sum' => 2],
                        ['id' => 2, 'month' => '07', 'year' => '2021', 'category_name' => 'category1', 'sum' => 7]
                    ]
                ));
                    
        $command = Command::ExpensesFromDatetime;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: []);
        $this->expectException(InvalidInputException::class);
        $commandService->handle();
    }

    public function testExpensesFromDatetimeWhenFullDateProvided(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $currentDatetime = date('Y-m-d H:i:s');
        $dbMock->method('execute')
                ->willReturn(
                    [
                        ['id' => 4, 'created_at' => $currentDatetime, 'amount' => 5, 'category_name' => 'test', 'note' => 'this_is_a_note']
                    ],
                );

        $command = Command::ExpensesFromDatetime;
        $commandService = new CommandService(db: $dbMock, userId: 1, command: $command, arguments: [date('d.m.Y', strtotime('-3 day'))]);
        $result = $commandService->handle();

        $dateValidator = new DateValidator(date: date('d.m.Y', strtotime('-3 day')), allowEmptyDate: true);
        $dateInfo = $dateValidator->validate();

        $daysPassedUntillNow = round((time() - strtotime($dateInfo['startDate'].' 00:00:00')) / 60 / 60 / 24);
        $avg = number_format(5 / $daysPassedUntillNow, 2);

        $toCompare = [date('d.m.Y H:i:s', strtotime($currentDatetime)).' (/delete_expense4) - 5р, test, this_is_a_note.', 'Итого: '.$avg.'р. в среднем за день.', 'Итого: 5р.'];

        $this->assertEquals($result, implode(PHP_EOL, $toCompare));
    }
}