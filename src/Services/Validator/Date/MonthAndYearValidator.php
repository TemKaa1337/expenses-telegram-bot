<?php declare(strict_types = 1);

namespace App\Services\Validator\Date;

use App\Exception\InvalidInputException;
use App\Services\Validator\Validator;
use App\Messages\ErrorMessage;

class MonthAndYearValidator implements Validator
{
    private const MONTHS_IN_YEAR = 12;

    private readonly string $currentYear;
    private readonly string $currentMonth;
    private readonly string $date;


    public function __construct(
        string $date,
        private readonly bool $allowEmptyDate
    )
    {
        $this->date = str_replace(' ', '', $date);
        [$this->currentYear, $this->currentMonth] = explode('-', date('Y-m'));
    }

    public function validate(): array
    {
        $this->validateAllowedSymbols();
        $this->validateEmptyDate();
        $this->validateDotsCount();

        if ($this->date === '') {
            $lastDayNumberOfSpecifiedMonth = cal_days_in_month(CAL_GREGORIAN, (int) $this->currentMonth, (int) $this->currentYear);

            return [
                'startDate' => "{$this->currentYear}-{$this->currentMonth}-01",
                'endDate' => "{$this->currentYear}-{$this->currentMonth}-{$lastDayNumberOfSpecifiedMonth}"
            ];
        }

        if (strpos($this->date, '.') === false) {
            $this->validateMonthLenth($this->date);
            $validatedMonth = $this->validateMonth($this->date);

            $lastDayNumberOfSpecifiedMonth = cal_days_in_month(CAL_GREGORIAN, (int) $validatedMonth, (int) $this->currentYear);

            return [
                'startDate' => "{$this->currentYear}-{$validatedMonth}-01",
                'endDate' => "{$this->currentYear}-{$validatedMonth}-{$lastDayNumberOfSpecifiedMonth}"
            ];
        }
        
        [$month, $year] = explode('.', $this->date);
        $this->validateMonthLenth($month);
        $this->validateYearLenth($year);
        $validatedMonth = $this->validateMonth($month);
        $validatedYear = $this->validateYear($year);

        $lastDayNumberOfSpecifiedMonth = cal_days_in_month(CAL_GREGORIAN, (int) $validatedMonth, (int) $validatedYear);

        return [
            'startDate' => "{$validatedYear}-{$validatedMonth}-01",
            'endDate' => "{$validatedYear}-{$validatedMonth}-{$lastDayNumberOfSpecifiedMonth}"
        ];
    }

    private function validateAllowedSymbols(): void
    {
        // if (!preg_match("/^[0-9]+$/i", $this->inputDate)) {
        if ($this->date !== '' && !preg_match("/^[.\d]+$/i", $this->date)) {
            throw new InvalidInputException(ErrorMessage::IncorrectDateFormat->value);
        }
    }

    private function validateEmptyDate(): void
    {
        if ($this->date === '' && !$this->allowEmptyDate) {
            throw new InvalidInputException(ErrorMessage::IncorrectDateFormat->value);
        }
    }

    private function validateDotsCount(): void
    {
        if (substr_count($this->date, '.') > 1) {
            throw new InvalidInputException(ErrorMessage::IncorrectDateFormat->value);
        }
    }

    private function validateMonth(string $month): string
    {
        $monthAsInt = (int) $month;
        
        if ($monthAsInt > self::MONTHS_IN_YEAR || $monthAsInt <= 0) {
            throw new InvalidInputException(ErrorMessage::IncorrectDateFormat->value);
        }

        return strlen($month) === 1 ? '0'.$month : $month;
    }

    private function validateYear(string $year): string
    {
        $validatedYear = strlen($year) === 2 ? substr(date('Y'), 0, 2).$year : $year;

        if ((int) $validatedYear > (int) $this->currentYear || (int) $validatedYear < 2021) {
            throw new InvalidInputException(ErrorMessage::IncorrectDateFormat->value);
        }

        return $validatedYear;
    }

    private function validateMonthLenth(string $month): void
    {
        $monthLength = strlen($month);

        if ($monthLength !== 1 && $monthLength !== 2) {
            throw new InvalidInputException(ErrorMessage::IncorrectDateFormat->value);
        }
    }

    private function validateYearLenth(string $year): void
    {
        $yearLength = strlen($year);

        if ($yearLength !== 4 && $yearLength !== 2) {
            throw new InvalidInputException(ErrorMessage::IncorrectDateFormat->value);
        }
    }
}