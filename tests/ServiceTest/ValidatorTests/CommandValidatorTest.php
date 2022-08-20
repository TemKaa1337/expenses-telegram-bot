<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Services\Validator\CommandValidatorService;
use App\Command\Command;
use App\Exception\InvalidCommandException;
use App\Exception\InvalidInputException;

final class CommandValidatorTest extends TestCase
{
    private function getInput(string $message): array
    {
        return [
            'update_id' => 836780966,
            'message' => [
                'from' => [
                    'id' => 772517840,
                    'is_bot' => false,
                    'first_name' => 'Artem',
                    'username' => 'temkaawow',
                    'language_code' => 'ru'
                ],
                'chat' => [
                    'id' => 772517840,
                    'first_name' => 'Artem',
                    'username' => 'temkaawow',
                    'type' => 'private'
                ],
                'date' => strtotime(date('Y-m-d H:i:s')),
                'text' => $message
            ]
        ];
    }

    public function testStartCommandCorrectlyValidated(): void
    {
        $input = $this->getInput(Command::Start->value);

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();

        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::Start);
        $this->assertEquals($commandInfo['arguments'], []);

        $input = $this->getInput(Command::Start->value.' unused');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();

        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::Start);
        $this->assertEquals($commandInfo['arguments'], ['unused']);
    }

    public function testAddExpenseCommandWithNoteCorrectlyValidated(): void
    {
        $input = $this->getInput('12 кафе темпо ужин');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();

        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::AddExpense);
        $this->assertEquals($commandInfo['arguments'], ['12', 'кафе', 'темпо ужин']);
    }

    public function testAddExpenseCommandWithoutNoteCorrectlyValidated(): void
    {
        $input = $this->getInput('12 кафе');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();

        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::AddExpense);
        $this->assertEquals($commandInfo['arguments'], ['12', 'кафе']);
    }

    public function testIfAddExpenseCommandWithoutCategoryWillFail(): void
    {
        $input = $this->getInput('12.1');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $this->expectException(InvalidCommandException::class);
        $commandValidator->validate();
    }

    public function testMonthExpensesCommandCorrectlyValidated(): void
    {
        $input = $this->getInput(Command::MonthExpenses->value);

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();
        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::MonthExpenses);
        $this->assertEquals($commandInfo['arguments'], []);

        $input = $this->getInput(Command::MonthExpenses->value.' month');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();
        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::MonthExpenses);
        $this->assertEquals($commandInfo['arguments'], ['month']);

        $input = $this->getInput(Command::MonthExpenses->value.' month.year');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();
        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::MonthExpenses);
        $this->assertEquals($commandInfo['arguments'], ['month.year']);
    }

    public function testDayExpensesCommandCorrectlyValidated(): void
    {
        $input = $this->getInput(Command::DayExpenses->value);

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();
        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::DayExpenses);
        $this->assertEquals($commandInfo['arguments'], []);

        $input = $this->getInput(Command::DayExpenses->value.' day');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();
        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::DayExpenses);
        $this->assertEquals($commandInfo['arguments'], ['day']);

        $input = $this->getInput(Command::DayExpenses->value.' day.month');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();
        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::DayExpenses);
        $this->assertEquals($commandInfo['arguments'], ['day.month']);

        $input = $this->getInput(Command::DayExpenses->value.' day.month.year');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();
        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::DayExpenses);
        $this->assertEquals($commandInfo['arguments'], ['day.month.year']);
    }

    public function testDeleteExpenseCommandCorrectlyValidated(): void
    {
        $input = $this->getInput(Command::DeleteExpense->value.'1');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();
        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::DeleteExpense);
        $this->assertEquals($commandInfo['arguments'], ['1']);

        $input = $this->getInput(Command::DeleteExpense->value.' 1');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();
        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::DeleteExpense);
        $this->assertEquals($commandInfo['arguments'], ['1']);

        $input = $this->getInput(Command::DeleteExpense->value.'1 1');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();
        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::DeleteExpense);
        $this->assertEquals($commandInfo['arguments'], ['1', '1']);
    }

    public function testAllAliasesCommandCorrectlyValidated(): void
    {
        $input = $this->getInput(Command::AllAliases->value);

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();
        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::AllAliases);
        $this->assertEquals($commandInfo['arguments'], []);

        $input = $this->getInput(Command::AllAliases->value.' unused');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();
        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::AllAliases);
        $this->assertEquals($commandInfo['arguments'], ['unused']);
    }

    public function testSpecificAliasesCommandCorrectlyValidated(): void
    {
        $input = $this->getInput(Command::SpecificAliases->value.' category_name');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();
        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::SpecificAliases);
        $this->assertEquals($commandInfo['arguments'], ['category_name']);

        $input = $this->getInput(Command::SpecificAliases->value);

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $this->expectException(InvalidCommandException::class);
        $commandValidator->validate();
    }

    public function testAddCategoryCommandCorrectlyValidated(): void
    {
        $input = $this->getInput(Command::AddCategory->value.' category_name');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();
        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::AddCategory);
        $this->assertEquals($commandInfo['arguments'], ['category_name']);

        $input = $this->getInput(Command::AddCategory->value);

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $this->expectException(InvalidCommandException::class);
        $commandValidator->validate();
    }

    public function testAddCategoryAliasCommandCorrectlyValidated(): void
    {
        $input = $this->getInput(Command::AddCategoryAlias->value.' category new_alias');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();
        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::AddCategoryAlias);
        $this->assertEquals($commandInfo['arguments'], ['category', 'new_alias']);

        $input = $this->getInput(Command::AddCategoryAlias->value.' category');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $this->expectException(InvalidCommandException::class);
        $commandValidator->validate();

        $input = $this->getInput(Command::AddCategoryAlias->value);

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $this->expectException(InvalidCommandException::class);
        $commandValidator->validate();
    }

    public function testDeleteCategoryCommandCorrectlyValidated(): void
    {
        $input = $this->getInput(Command::DeleteCategory->value.' category_name');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();
        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::DeleteCategory);
        $this->assertEquals($commandInfo['arguments'], ['category_name']);

        $input = $this->getInput(Command::DeleteCategory->value);

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $this->expectException(InvalidCommandException::class);
        $commandValidator->validate();

        $input = $this->getInput(Command::DeleteCategory->value.' category_name unused');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();
        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::DeleteCategory);
        $this->assertEquals($commandInfo['arguments'], ['category_name', 'unused']);
    }

    public function testDeleteCategoryAliasCommandCorrectlyValidated(): void
    {
        $input = $this->getInput(Command::DeleteCategoryAlias->value.' category_name alias');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();
        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::DeleteCategoryAlias);
        $this->assertEquals($commandInfo['arguments'], ['category_name', 'alias']);

        $input = $this->getInput(Command::DeleteCategoryAlias->value);

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $this->expectException(InvalidCommandException::class);
        $commandValidator->validate();

        $input = $this->getInput(Command::DeleteCategoryAlias->value.' category_name');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $this->expectException(InvalidCommandException::class);
        $commandValidator->validate();

        $input = $this->getInput(Command::DeleteCategoryAlias->value.' category_name alias unused');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();
        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::DeleteCategoryAlias);
        $this->assertEquals($commandInfo['arguments'], ['category_name', 'alias', 'unused']);
    }

    public function testMonthExpensesByCategoryCommandCorrectlyValidated(): void
    {
        $input = $this->getInput(Command::MonthExpensesByCategory->value);

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();
        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::MonthExpensesByCategory);
        $this->assertEquals($commandInfo['arguments'], []);

        $input = $this->getInput(Command::MonthExpensesByCategory->value.' month');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();
        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::MonthExpensesByCategory);
        $this->assertEquals($commandInfo['arguments'], ['month']);

        $input = $this->getInput(Command::MonthExpensesByCategory->value.' month.year');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();
        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::MonthExpensesByCategory);
        $this->assertEquals($commandInfo['arguments'], ['month.year']);
    }

    public function testAverageEachMonthExpensesCommandCorrectlyValidated(): void
    {
        $input = $this->getInput(Command::AverageEachMonthExpenses->value.' unused');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();
        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::AverageEachMonthExpenses);
        $this->assertEquals($commandInfo['arguments'], ['unused']);

        $input = $this->getInput(Command::AverageEachMonthExpenses->value);

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();
        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::AverageEachMonthExpenses);
        $this->assertEquals($commandInfo['arguments'], []);
    }

    public function testTotalMonthExpensesCommandCorrectlyValidated(): void
    {
        $input = $this->getInput(Command::TotalMonthExpenses->value.' unused');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();
        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::TotalMonthExpenses);
        $this->assertEquals($commandInfo['arguments'], ['unused']);

        $input = $this->getInput(Command::TotalMonthExpenses->value);

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();
        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::TotalMonthExpenses);
        $this->assertEquals($commandInfo['arguments'], []);
    }

    public function testMonthExpensesFromDateCommandCorrectlyValidated(): void
    {
        $input = $this->getInput(Command::ExpensesFromDatetime->value.' day');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();
        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::ExpensesFromDatetime);
        $this->assertEquals($commandInfo['arguments'], ['day']);

        $input = $this->getInput(Command::ExpensesFromDatetime->value.' day.month');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();
        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::ExpensesFromDatetime);
        $this->assertEquals($commandInfo['arguments'], ['day.month']);

        $input = $this->getInput(Command::ExpensesFromDatetime->value.' day.month.year');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $commandInfo = $commandValidator->validate();
        $this->assertArrayHasKey('command', $commandInfo, 'Validated array hasnt command key');
        $this->assertArrayHasKey('arguments', $commandInfo, 'Validated array hasnt arguments key');
        $this->assertEquals($commandInfo['command'], Command::ExpensesFromDatetime);
        $this->assertEquals($commandInfo['arguments'], ['day.month.year']);

        $input = $this->getInput(Command::ExpensesFromDatetime->value);

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $this->expectException(InvalidCommandException::class);
        $commandValidator->validate();
    }

    public function testIncorrectCommandException(): void
    {
        $input = $this->getInput('/wrong_command param1');

        $commandValidator = new CommandValidatorService(command: $input['message']['text']);
        $this->expectException(InvalidCommandException::class);
        $commandValidator->validate();
    }
}