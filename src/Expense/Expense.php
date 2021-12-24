<?php
declare(strict_types = 1);

namespace App\Expense;

use App\Exception\InvalidInputException;
use App\Categories\Categories;
use App\Database\Database;
use App\Http\Request;
use App\Model\User;
use Exception;

class Expense
{
    private User $user;
    private Database $db;
    private string|float $amount;
    private int $categoryId;

    public function __construct(User $user, Database $db, int $categoryId, string|float $message)
    {
        $this->db = $db;
        $this->user = $user;
        $this->categoryId = $categoryId;
        $this->amount = $message;
    }

    public function addExpense() : string
    {
        $note = $this->getNote($this->amount);
        $this->amount = $this->getAmount($this->amount);
        $this->user->addExpense($this->amount, $this->categoryId, $note);

        return 'Новая трата добавлена успешно!';
    }

    public function getMonthExpenses(string $arguments) : string
    {
        $expenses = $this->user->getMonthExpenses($arguments);

        if (empty($expenses)) return 'В этом месяце еще не было трат!';

        $result = [];
        $totalSumm = 0;

        foreach ($expenses as $expense) {
            $result[] = date('d.m.Y H:i:s', strtotime($expense['created_at']))." (/delete{$expense['id']}) - {$expense['amount']}р, {$expense['category_name']}".$this->getNoteForOutput($expense['note']);

            $totalSumm += $expense['amount'];
        }

        $avg = number_format($totalSumm / (int)date('d'), 2);
        $result[] = "Итого {$avg}р. в среднем за день";
        $result[] = "Итого {$totalSumm}р.";

        return implode(PHP_EOL, $result);
    }

    public function getMonthExpensesByCategory(string $arguments) : string
    {
        $expenses = $this->user->getMonthExpensesByCategory($arguments);

        if (empty($expenses)) return 'В этом месяце еще не было трат!';

        $total = 0;
        $result = [];
        $output = [];

        foreach ($expenses as $expense) {
            if (isset($result[$expense['category_name']])) {
                $result[$expense['category_name']] += (float)$expense['amount'];
            } else {
                $result[$expense['category_name']] = (float)$expense['amount'];
            }
        }

        foreach ($result as $category => $value) {
            $output[] = "{$category}: {$value}р.";

            $total += $value;
        }

        $total = round($total, 2);
        $output[] = "Итого: {$total}р.";

        return implode(PHP_EOL, $output);
    }

    public function getDayExpenses() : string
    {
        $expenses = $this->user->getDayExpenses();

        if (empty($expenses)) return 'На сегодня трат нет!';

        $result = [];
        $totalSumm = 0;

        foreach ($expenses as $expense) {
            $result[] = date('H:i:s', strtotime($expense['created_at']))." (/delete{$expense['id']}) - {$expense['amount']}р, {$expense['category_name']}".$this->getNoteForOutput($expense['note']);

            $totalSumm += $expense['amount'];
        }

        $result[] = "Итого {$totalSumm}р.";

        return implode(PHP_EOL, $result);
    }

    public function getPreviousMonthExpenses(string $arguments) : string
    {
        $expenses = $this->user->getPreviousMonthExpenses($arguments);
        
        if (empty($expenses)) return 'В прошлом месяце не было трат!';

        $result = [];
        $totalSumm = 0;

        foreach ($expenses as $expense) {
            $result[] = date('d.m.Y H:i:s', strtotime($expense['created_at']))." (/delete{$expense['id']}) - {$expense['amount']}р, {$expense['category_name']}".$this->getNoteForOutput($expense['note']);

            $totalSumm += $expense['amount'];
        }

        $result[] = "Итого {$totalSumm}р.";

        return implode(PHP_EOL, $result);
    }

    public function getAverageMonthExpensesByCategory(string $arguments): string
    {
        $result = [];
        $average = [];
        $categories = [];
        $where = strpos($arguments, '-s') !== false ? "" : " AND categories.category_name != 'Steam' ";
        $expenses = $this->db->execute("
            select 
                categories.category_name, 
                extract(month from expenses.created_at) as month, 
                extract(year from expenses.created_at) as year, 
                sum(expenses.amount) 
            from expenses 
            join categories on expenses.category_id = categories.id 
            where expenses.user_id = ? $where 
            group by 
                extract(month from expenses.created_at), 
                extract(year from expenses.created_at), 
                categories.category_name 
            order by
                year, 
                month desc;
        ", [$this->user->getUserId()]);
        
        if (empty($expenses)) return 'Трат не обнаружено!';

        foreach ($expenses as $expense) {
            if (!in_array($expense['category_name'], $categories))
                $categories[] = $expense['category_name'];

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
                if (isset($monthExpenses[$category])) {
                    $temp[] = $monthExpenses[$category].'р.';
                } else $temp[] = '0р.';
            }

            $average[] = "$category: ".implode('|', $temp);
        }

        return implode(PHP_EOL, $average); 
    }

    public function getTotalMonthsExpenses(string $arguments): string
    {
        $result = [];
        $where = strpos($arguments, '-s') !== false ? "" : " AND categories.category_name != 'Steam' ";
        $expenses = $this->db->execute("
            select 
                extract(month from expenses.created_at) as month, 
                extract(year from expenses.created_at) as year, 
                sum(expenses.amount) 
            from expenses 
            join categories on expenses.category_id = categories.id 
            where expenses.user_id = ? $where 
            group by 
                extract(month from expenses.created_at), 
                extract(year from expenses.created_at) 
            order by 
                year, 
                month desc;
        ", [$this->user->getUserId()]);

        if (empty($expenses)) return 'Трат не обнаружено!';

        foreach ($expenses as $expense) {
            $result[] = "{$expense['month']}.{$expense['year']} - {$expense['sum']}р.";
        }

        return implode(PHP_EOL, $result);
    }

    public function getMonthExpensesFromDate(string $arguments): string
    {
        if ($arguments === '') throw new InvalidInputException('Неправильный формат сообщения.');

        $day = (int) $arguments;
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int) date('m'), (int) date('Y'));

        if ($day < 1 || $day > $daysInMonth) throw new InvalidInputException('Вы ввели неправильный день.');

        $date = date('Y-m').$day.' 00:00:00';

        if (strpos($arguments, ' ') !== false) {
            $arguments = '';
        } else {
            $arguments = str_replace((string) $day, $arguments, '');
        }
        
        $expenses = $this->user->getMonthExpensesFromDate($date, $arguments);

        if (empty($expenses)) return 'В этом месяце еще не было трат!';

        $result = [];
        $totalSumm = 0;

        foreach ($expenses as $expense) {
            $result[] = date('d.m.Y H:i:s', strtotime($expense['created_at']))." (/delete{$expense['id']}) - {$expense['amount']}р, {$expense['category_name']}".$this->getNoteForOutput($expense['note']);

            $totalSumm += $expense['amount'];
        }

        $avg = number_format($totalSumm / (int)date('d'), 2);
        $result[] = "Итого {$avg}р. в среднем за день";
        $result[] = "Итого {$totalSumm}р.";

        return implode(PHP_EOL, $result);
    }

    public function deleteExpense(int $expenseId) : string
    {
        $this->user->deleteExpense($expenseId);

        return 'Трата успешно удалена!';
    }

    public function getAmount(string $message) : float
    {
        if (strpos($message, ' ') !== false) {
            $message = explode(' ', $message);
            
            if (is_numeric($message[0])) return floatval($message[0]);
            else throw new InvalidInputException('Неправильный формат суммы.');
        } else throw new InvalidInputException('Неправильный формат сообщения.');
    }

    public function getNote(string $message) : ?string
    {
        if (strpos($message, ' ') !== false) {
            $message = explode(' ', $message);
            
            if (count($message) > 2) {
                return implode(' ', array_slice($message, 2, count($message) - 2));
            } else return null;
        } else throw new InvalidInputException('Неправильный формат сообщения.');
    }

    public function getNoteForOutput(?string $note) : string
    {
        if ($note === null) return '';

        return ", {$note}.";
    }

    public function isUserAllowedToDeleteExpense(int $expenseId) : bool
    {
        $isAllowed = $this->db->execute('SELECT user_id FROM expenses WHERE id = ?', [$expenseId]);

        if (empty($isAllowed)) return false;

        return $isAllowed[0]['user_id'] === $this->user->getUserId();
    }
}

?>