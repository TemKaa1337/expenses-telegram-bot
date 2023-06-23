<?php declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ExpenseNotFoundException;
use App\Messages\Error;
use App\Models\Expense as ExpenseModel;
use App\Models\User;

final readonly class Expense
{
    /**
     * @param Database $db
     * @param User $user
     */
    public function __construct(
        private Database $db,
        private User $user
    ) {}

    /**
     * @param int $id
     * @return ExpenseModel
     * @throws ExpenseNotFoundException
     */
    public function find(int $id): ExpenseModel
    {
        $expenseInfo = $this->db->execute(
            <<<SQL
                SELECT * FROM expenses WHERE user_id = %s AND id = %s
            SQL,
            [$this->user->id, $id]
        );
        if (empty($expenseInfo)) {
            throw new ExpenseNotFoundException(Error::NoSuchExpense->value);
        }

        return new ExpenseModel(
            $expenseInfo[0]['id'],
            $expenseInfo[0]['user_id'],
            $expenseInfo[0]['category_id'],
            $expenseInfo[0]['amount'],
        );
    }

    /**
     * @param float $amount
     * @param int $categoryId
     * @param string|null $note
     * @return void
     */
    public function create(
        float $amount,
        int $categoryId,
        string|null $note
    ): void
    {
        $this->db->execute(
            <<<SQL
                INSERT INTO expenses(created_at, amount, user_id, category_id, note) VALUES (?, ?, ?, ?, ?)
            SQL,
            [date('Y-m-d H:i:s'), $amount, $this->user->id, $categoryId, $note]
        );
    }

    /**
     * @param ExpenseModel $expense
     * @return void
     */
    public function delete(ExpenseModel $expense): void
    {
        $this->db->execute(
            <<<SQL
                DELETE FROM expenses WHERE user_id = %s AND id = %s
            SQL,
            [$this->user->id, $expense->id]
        );
    }

    /**
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     * @throws ExpenseNotFoundException
     */
    public function getAllBetweenDates(
        string $dateFrom,
        string $dateTo
    ): array
    {
        $datetimeFrom = $dateFrom.' 00:00:00';
        $datetimeTo = $dateTo.' 23:59:59';
        $expenses = $this->db->execute(
            <<<SQL
                SELECT 
                    expenses.*, 
                    categories.category_name 
                FROM 
                    expenses 
                JOIN 
                    categories 
                ON 
                    expenses.category_id = categories.id 
                WHERE 
                    expenses.user_id = ? 
                    AND created_at >= ? 
                    AND created_at <= ? 
                ORDER BY 
                    expenses.id
            SQL,
            [$this->user->id, $datetimeFrom, $datetimeTo]
        );
        if (empty($expenses)) {
            throw new ExpenseNotFoundException(Error::NoExpensesFoundForGivenPeriod->value);
        }
        return $expenses;
    }

    /**
     * @return array
     * @throws ExpenseNotFoundException
     */
    public function getEachMonthExpenses(): array
    {
        $expensesInfo = $this->db->execute(
            <<<SQL
                SELECT 
                    extract(month from expenses.created_at) as month, 
                    extract(year from expenses.created_at) as year, 
                    sum(expenses.amount) 
                FROM 
                    expenses 
                JOIN 
                    categories 
                ON 
                    expenses.category_id = categories.id 
                WHERE 
                    expenses.user_id = ?
                GROUP BY 
                    extract(month from expenses.created_at), 
                    extract(year from expenses.created_at) 
                ORDER BY 
                    year desc, 
                    month desc
            SQL,
            [$this->user->id]
        );
        if (empty($expensesInfo)) {
            throw new ExpenseNotFoundException(Error::NoExpensesFoundForGivenPeriod->value);
        }
        return $expensesInfo;
    }
}