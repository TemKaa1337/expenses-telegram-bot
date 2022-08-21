<?php declare(strict_types = 1);

namespace App\Services;

use App\Database\Database;
use App\Exception\NoExpenseFoundException;
use App\Model\Category;
use App\Model\User;
use App\Model\Expense;
use App\Messages\ErrorMessage;

class ExpenseService
{
    public function __construct(
        private readonly Database $db,
        private readonly User $user
    ) {}

    public function addExpense(
        Category $category,
        float $amount,
        string|null $note
    ): void
    {
        $category->checkIfCategoryExists();
        $this->db->execute("
            INSERT INTO 
                expenses (created_at, amount, user_id, category_id, note) 
            VALUES (?, ?, ?, ?, ?)
        ", [date('Y-m-d H:i:s'), $amount, $this->user->getDatabaseUserId(), $category->getCategoryId(), $note]);
    }

    public function delete(int $expenseId): void
    {
        $expense = new Expense(db: $this->db, user: $this->user, expenseId: $expenseId);
        $expense->delete();
    }

    public function getSpecificMonthExpenses(
        array $arrayOfFlags,
        string $dateFrom,
        string $dateTo
    ): array
    {
        $groupFlagExist = in_array('-g', $arrayOfFlags);
        $showFlagExist = in_array('-s', $arrayOfFlags);

        $groupBySql = $groupFlagExist ? 'GROUP BY categories.category_name, expenses.id' : '';
        $additionalWhereSql = $showFlagExist ? "AND categories.category_name not in ('CyberShoke', 'Steam')" : '';
        
        $expenses = $this->db->execute(
            "
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
                    {$additionalWhereSql}
                {$groupBySql}
                ORDER BY 
                    expenses.id asc
            ", 
            [$this->user->getDatabaseUserId(), $dateFrom, $dateTo]
        );

        if (empty($expenses)) {
            throw new NoExpenseFoundException(ErrorMessage::NoExpensesFoundForGivenPeriod->value);
        }

        return $expenses;
    }

    public function getSpecificDayExpenses(
        array $arrayOfFlags,
        string $datetimeFrom,
        string $datetimeTo
    ): array
    {
        $groupFlagExist = in_array('-g', $arrayOfFlags);
        $showFlagExist = in_array('-s', $arrayOfFlags);
        $groupBySql = $groupFlagExist ? 'GROUP BY categories.category_name, expenses.id' : '';
        $additionalWhereSql = $showFlagExist ? "AND categories.category_name not in ('CyberShoke', 'Steam')" : '';

        $expenses = $this->db->execute(
            "
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
                    {$additionalWhereSql}
                {$groupBySql}
                ORDER BY 
                    expenses.id asc
            ", 
            [$this->user->getDatabaseUserId(), $datetimeFrom, $datetimeTo]
        );
        
        if (empty($expenses)) {
            throw new NoExpenseFoundException(ErrorMessage::NoExpensesFoundForGivenPeriod->value);
        }

        return $expenses;
    }

    public function getMonthExpensesByCategory(
        array $arrayOfFlags,
        string $datetimeFrom,
        string $datetimeTo
    ): array
    {
        $showFlagExist = in_array('-s', $arrayOfFlags);
        $additionalWhereSql = $showFlagExist ? "AND categories.category_name not in ('CyberShoke', 'Steam')" : '';
    
        $expenses = $this->db->execute(
            "
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
                    {$additionalWhereSql} 
                ORDER BY 
                    expenses.id asc
            ",
            [$this->user->getDatabaseUserId(), $datetimeFrom, $datetimeTo]
        );

        if (empty($expenses)) {
            throw new NoExpenseFoundException(ErrorMessage::NoExpensesFoundForGivenPeriod->value);
        }

        return $expenses;
    }
    
    public function getAverageMonthExpenses(array $arrayOfFlags): array
    {
        $showFlagExist = in_array('-s', $arrayOfFlags);
        $additionalWhereSql = $showFlagExist ? "AND categories.category_name not in ('CyberShoke', 'Steam')" : '';
        
        $expenses = $this->db->execute("
                SELECT 
                    categories.category_name, 
                    extract(month from expenses.created_at) as month, 
                    extract(year from expenses.created_at) as year, 
                    sum(expenses.amount) 
                FROM expenses 
                JOIN
                categories 
                ON 
                    expenses.category_id = categories.id 
                WHERE 
                    expenses.user_id = ?
                    {$additionalWhereSql}
                GROUP BY 
                    extract(month from expenses.created_at), 
                    extract(year from expenses.created_at), 
                    categories.category_name 
                ORDER BY
                    year desc,
                    month desc
            ",
            [$this->user->getDatabaseUserId()]
        );

        if (empty($expenses)) {
            throw new NoExpenseFoundException(ErrorMessage::NoExpensesFoundForGivenPeriod->value);
        }

        return $expenses;
    }

    public function getTotalMonthExpenses(array $arrayOfFlags): array
    {
        $showFlagExist = in_array('-s', $arrayOfFlags);
        $additionalWhereSql = $showFlagExist ? "AND categories.category_name not in ('CyberShoke', 'Steam')" : '';

        $expenses = $this->db->execute(
            "
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
                    {$additionalWhereSql} 
                GROUP BY 
                    extract(month from expenses.created_at), 
                    extract(year from expenses.created_at) 
                ORDER BY 
                    year desc, 
                    month desc;
            ",
            []
        );

        if (empty($expenses)) {
            throw new NoExpenseFoundException(ErrorMessage::NoExpensesFoundForGivenPeriod->value);
        }

        return $expenses;
    }

    public function getExpensesFromSpecificDatetime(
        array $arrayOfFlags,
        string $datetimeFrom
    ): array
    {
        $groupFlagExist = in_array('-g', $arrayOfFlags);
        $showFlagExist = in_array('-s', $arrayOfFlags);
        $groupBySql = $groupFlagExist ? 'GROUP BY categories.category_name, expenses.id' : '';
        $additionalWhereSql = $showFlagExist ? "AND categories.category_name not in ('CyberShoke', 'Steam')" : '';

        $expenses = $this->db->execute(
            "
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
                    {$additionalWhereSql} 
                {$groupBySql}
                ORDER BY 
                    expenses.id asc
            ",
            [$this->user->getDatabaseUserId(), $datetimeFrom]
        );

        if (empty($expenses)) {
            throw new NoExpenseFoundException(ErrorMessage::NoExpensesFoundForGivenPeriod->value);
        }

        return $expenses;
    }
}