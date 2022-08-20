<?php
declare(strict_types = 1);

namespace App\Services;

use App\Command\Command;
use App\Database\Database;
use App\Exception\InvalidInputException;
use App\Messages\SuccessMessage;
use App\Model\User;
use App\Model\Category;
use App\Model\CategoryAlias;
use App\Model\CategoryAliases;
use App\Services\ExpenseService;
use App\Services\Validator\Date\DateValidator;
use App\Services\Validator\Date\MonthAndYearValidator;

class CommandService
{
    public function __construct(
        private readonly Database $db,
        private readonly User $user,
        private readonly Command $command,
        private readonly array $arguments
    )
    { }

    public function handle(): string
    {
        switch ($this->command) {
            case Command::Start:
                $commands = Command::cases();
                $output = [];

                foreach ($commands as $command) {
                    $description = $command->getDescription();
                    $output[] = "{$command->value} {$description}";
                }

                $output = array_merge(
                    $output,
                    [
                        'Для того, чтобы добавить трату вводите в формате: {сумма траты (например, 14.1)} {название или алиас раздела} {примечание}.',
                        'Пример: 14.5 продукты ничего тольком не купил',
                        'Для того, чтобы добавить категорию расходов, введите данные в формате: /add_category {CategoryName}.',
                        'Пример: /add_category Бензин',
                        'Для того, чтобы добавить алиас для категории расходов, введите данные в формате: /add_category_alias {CategoryName} {Alias}.',
                        'Пример: /add_category_alias Бензин бенз (важно, что слово, стоящее сразу после команды /add_category_alias, должно быть таким же по написанию, как вы добавляли через /add_category)'
                    ]
                );
                
                return implode(PHP_EOL, $output);
            case Command::AddExpense:
                $amount = (float) $this->arguments[0];
                $categoryName = $this->arguments[1];
                $note = $this->arguments[2] ?? null;
                
                $category = new Category(db: $this->db, user: $this->user, categoryName: $categoryName);
                $expenseService = new ExpenseService(db: $this->db, user: $this->user);
                $expenseService->addExpense(
                    category: $category,
                    amount: $amount,
                    note: $note
                );

                return SuccessMessage::ExpenseAdded->value;
            case Command::DayExpenses:
                $dateValidator = new DateValidator(date: $this->arguments[0] ?? '', allowEmptyDate: true);
                $dateInfo = $dateValidator->validate();

                $datetimeFrom = $dateInfo['startDate'].' 00:00:00';
                $datetimeTo = $dateInfo['startDate'].' 23:59:59';

                $arrayOfFlags = $this->arguments;
                if (!empty($this->arguments)) {
                    array_shift($arrayOfFlags);
                }

                $expenseService = new ExpenseService(db: $this->db, user: $this->user);
                $expenses = $expenseService->getSpecificDayExpenses(
                    arrayOfFlags: $arrayOfFlags,
                    datetimeFrom: $datetimeFrom,
                    datetimeTo: $datetimeTo
                );
                
                $output = [];
                $total = 0;
                foreach ($expenses as $expense) {
                    $date = date('H:i:s', strtotime($expense['created_at']));
                    $commandToDelete = "(/delete{$expense['id']})";
                    $amountAndCategory = "{$expense['amount']}р, {$expense['category_name']}";
                    $note = $expense['note'] !== null ? ", {$expense['note']}." : '';

                    $output[] = $date.' '.$commandToDelete.' - '.$amountAndCategory.$note;
                    $total += $expense['amount'];
                }

                $output[] = "Итого: {$total}р.";

                return implode(PHP_EOL, $output);
            case Command::MonthExpenses:
                $dateValidator = new MonthAndYearValidator(date: $this->arguments[0] ?? '', allowEmptyDate: true);
                $dateInfo = $dateValidator->validate();

                $datetimeFrom = $dateInfo['startDate'].' 00:00:00';
                $datetimeTo = $dateInfo['endDate'].' 23:59:59';

                $arrayOfFlags = $this->arguments;
                if (!empty($this->arguments)) {
                    array_shift($arrayOfFlags);
                }

                $expenseService = new ExpenseService(db: $this->db, user: $this->user);
                $expenses = $expenseService->getSpecificMonthExpenses(
                    arrayOfFlags: $arrayOfFlags,
                    dateFrom: $datetimeFrom,
                    dateTo: $datetimeTo
                );
                
                $output = [];
                $total = 0;
                foreach ($expenses as $expense) {
                    $date = date('d.m.Y H:i:s', strtotime($expense['created_at']));
                    $commandToDelete = "(/delete{$expense['id']})";
                    $amountAndCategory = "{$expense['amount']}р, {$expense['category_name']}";
                    $note = $expense['note'] !== null ? ", {$expense['note']}." : '';

                    $output[] = $date.' '.$commandToDelete.' - '.$amountAndCategory.$note;
                    $total += $expense['amount'];
                }

                $daysPassedUntillNow = round((time() - strtotime($datetimeFrom)) / 60 / 60 / 24);
                $avg = number_format($total / $daysPassedUntillNow, 2);
                $output[] = "Итого: {$avg}р. в среднем за день.";
                $output[] = "Всего: {$total}р.";

                return implode(PHP_EOL, $output);
            case Command::DeleteExpense:
                $expenseId = (int) $this->arguments[0];
                $expenseService = new ExpenseService(db: $this->db, user: $this->user);
                $expenseService->delete(expenseId: $expenseId);

                return SuccessMessage::ExpenseDeleted->value;
            case Command::AllAliases:
                $categoryAliases = new CategoryAliases(db: $this->db, user: $this->user);
                $aliases =  $categoryAliases->getAllALiases();

                $output = [];
                $lastCategory = null;
                foreach ($aliases as $alias) {
                    if ($alias['category_name'] !== $lastCategory) {
                        $lastCategory = $alias['category_name'];
                        $output[] = "Список алиасов для категории {$lastCategory}:";
                    }

                    $output[] = ' - '.$alias['alias'];
                }
                
                return implode(PHP_EOL, $output);
            case Command::SpecificAliases:
                $categoryName = $this->arguments[0];
                $category = new Category(db: $this->db, user: $this->user, categoryName: $categoryName);
                $aliases = $category->getAliases();

                $output = ["Список алиасов для категории {$categoryName}:"];

                foreach ($aliases as $alias) {
                    $output[] = ' - '.$alias['alias'];
                }
                
                return implode(PHP_EOL, $output);
            case Command::AddCategory:
                $categoryName = $this->arguments[0];
                $category = new Category(db: $this->db, user: $this->user, categoryName: $categoryName);
                $category->add();

                return SuccessMessage::CategoryAdded->value;
            case Command::AddCategoryAlias:
                $userCategoryName = $this->arguments[0];
                $userCategoryAlias = $this->arguments[1];
                $category = new Category(db: $this->db, user: $this->user, categoryName: $userCategoryName);
                $categoryAlias = new CategoryAlias(db: $this->db, category: $category, alias: $userCategoryAlias);
                $categoryAlias->add();

                return SuccessMessage::CategoryAliasAdded->value;
            case Command::DeleteCategory:
                $categoryName = $this->arguments[0];
                $category = new Category(db: $this->db, user: $this->user, categoryName: $categoryName);
                $category->delete();

                return SuccessMessage::CategoryDeleted->value;
            case Command::DeleteCategoryAlias:
                $userCategoryName = $this->arguments[0];
                $userCategoryAlias = $this->arguments[1];
                $category = new Category(db: $this->db, user: $this->user, categoryName: $userCategoryName);
                $categoryAlias = new CategoryAlias(db: $this->db, category: $category, alias: $userCategoryAlias);
                $categoryAlias->delete();

                return SuccessMessage::CategoryAliasDeleted->value;
            case Command::MonthExpensesByCategory:
                $dateValidator = new MonthAndYearValidator(date: $this->arguments[0] ?? '', allowEmptyDate: true);
                $dateInfo = $dateValidator->validate();

                $datetimeFrom = $dateInfo['startDate'].' 00:00:00';
                $datetimeTo = $dateInfo['endDate'].' 23:59:59';

                $arrayOfFlags = $this->arguments;
                if (!empty($this->arguments)) {
                    array_shift($arrayOfFlags);
                }

                $expenseService = new ExpenseService(db: $this->db, user: $this->user);
                $expenses = $expenseService->getMonthExpensesByCategory(
                    arrayOfFlags: $arrayOfFlags,
                    datetimeTo: $datetimeTo,
                    datetimeFrom: $datetimeFrom
                );
        
                $total = 0;
                $groupedExpenses = [];
                $output = [];
        
                foreach ($expenses as $expense) {
                    if (isset($groupedExpenses[$expense['category_name']])) {
                        $groupedExpenses[$expense['category_name']] += (float) $expense['amount'];
                    } else {
                        $groupedExpenses[$expense['category_name']] = (float) $expense['amount'];
                    }
                }
        
                foreach ($groupedExpenses as $category => $value) {
                    $output[] = "{$category}: {$value}р.";
                    $total += $value;
                }
        
                $total = round($total, 2);
                $output[] = "Итого: {$total}р.";
        
                return implode(PHP_EOL, $output);
            case Command::AverageEachMonthExpenses:
                $expenseService = new ExpenseService(db: $this->db, user: $this->user);
                $expenses = $expenseService->getAverageMonthExpenses(arrayOfFlags: $this->arguments);

                $result = [];
                $average = [];
                $categories = [];
        
                foreach ($expenses as $expense) {
                    if (!in_array($expense['category_name'], $categories)) {
                        $categories[] = $expense['category_name'];
                    }
        
                    $month = $expense['year'].'.'.$expense['month'];
                    if (isset($result[$month])) {
                        $result[$month][$expense['category_name']] = $expense['sum'];
                    } else {
                        $result[$month] = [$expense['category_name'] => $expense['sum']];
                    }
                }
        
                foreach ($categories as $category) {
                    $temp = [];
                    foreach ($result as $month => $monthExpenses) {
                        $temp[] = isset($monthExpenses[$category]) ? $monthExpenses[$category].'р.' : '0р.';
                    }
        
                    $average[] = "$category: ".implode(' | ', $temp);
                }
        
                return implode(PHP_EOL, $average); 
            case Command::TotalMonthExpenses:
                $expenseService = new ExpenseService(db: $this->db, user: $this->user);
                $expenses = $expenseService->getTotalMonthExpenses(arrayOfFlags: $this->arguments);

                $output = [];
                foreach ($expenses as $expense) {
                    $output[] = "{$expense['month']}.{$expense['year']} - {$expense['sum']}р.";
                }

                return implode(PHP_EOL, $output);
            case Command::ExpensesFromDatetime:
                $dateValidator = new DateValidator(date: $this->arguments[0] ?? '', allowEmptyDate: false);
                $dateInfo = $dateValidator->validate();

                $datetimeFrom = $dateInfo['startDate'].' 00:00:00';

                $arrayOfFlags = $this->arguments;
                if (!empty($this->arguments)) {
                    array_shift($arrayOfFlags);
                }

                $expenseService = new ExpenseService(db: $this->db, user: $this->user);
                $expenses = $expenseService->getExpensesFromSpecificDatetime(
                    arrayOfFlags: $arrayOfFlags,
                    datetimeFrom: $datetimeFrom
                );
        
                $output = [];
                $total = 0;
        
                foreach ($expenses as $expense) {
                    $date = date('d.m.Y H:i:s', strtotime($expense['created_at']));
                    $commandToDelete = "(/delete{$expense['id']})";
                    $amountAndCategory = "{$expense['amount']}р, {$expense['category_name']}";
                    $note = $expense['note'] !== null ? ", {$expense['note']}." : '';
        
                    $output[] = $date.' '.$commandToDelete.' - '.$amountAndCategory.$note;
                    $total += $expense['amount'];
                }
        
                $daysPassedUntillNow = round((time() - strtotime($datetimeFrom)) / 60 / 60 / 24);
                $avg = number_format($total / $daysPassedUntillNow, 2);
                $output[] = "Итого: {$avg}р. в среднем за день.";
                $output[] = "Итого: {$total}р.";
        
                return implode(PHP_EOL, $output);
        }

        throw new InvalidInputException('Unknown exception.');
    }
}

?>