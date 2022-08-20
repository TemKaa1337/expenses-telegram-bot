<?php declare(strict_types = 1);

namespace App\Services\Validator\Date;

use App\Exception\InvalidInputException;
use App\Services\Validator\Validator;
use App\Messages\ErrorMessage;

class DateValidator implements Validator
{
    private const MONTHS_IN_YEAR = 12;

    private readonly string $currentYear;
    private readonly string $currentMonth;
    private readonly string $currentDay;
    private readonly string $date;

    public function __construct(
        string $date,
        private readonly bool $allowEmptyDate
    )
    {
        $this->date = str_replace(' ', '', $date);
        [$this->currentYear, $this->currentMonth, $this->currentDay] = explode('-', date('Y-m-d'));
    }

    public function validate(): array
    {
        $this->validateAllowedSymbols();
        $this->validateEmptyDate();
        $this->validateDotsCount();

        if ($this->date === '') {
            $lastDayNumberOfSpecifiedMonth = cal_days_in_month(CAL_GREGORIAN, (int) $this->currentMonth, (int) $this->currentYear);

            return [
                'startDate' => "{$this->currentYear}-{$this->currentMonth}-{$this->currentDay}",
                'endDate' => "{$this->currentYear}-{$this->currentMonth}-{$lastDayNumberOfSpecifiedMonth}"
            ];
        }

        if (strpos($this->date, '.') === false) {
            $this->validateDayLenth($this->date);
            $validatedDay = $this->validateDay($this->currentYear, $this->currentMonth, $this->date);

            $lastDayNumberOfSpecifiedMonth = cal_days_in_month(CAL_GREGORIAN, (int) $this->currentMonth, (int) $this->currentYear);

            return [
                'startDate' => "{$this->currentYear}-{$this->currentMonth}-{$validatedDay}",
                'endDate' => "{$this->currentYear}-{$this->currentMonth}-{$lastDayNumberOfSpecifiedMonth}"
            ];
        }
        
        if (substr_count($this->date, '.') == 1) {
            // d.m
            [$day, $month] = explode('.', $this->date);
            $year = $this->currentYear;
        } else {
            // d.m.Y
            [$day, $month, $year] = explode('.', $this->date);
        }
        

        $this->validateDayLenth($day);
        $this->validateMonthLenth($month);
        $this->validateYearLenth($year);

        $validatedYear = $this->validateYear($year);
        $validatedMonth = $this->validateMonth($month);
        $validatedDay = $this->validateDay($year, $validatedMonth, $day);

        $lastDayNumberOfSpecifiedMonth = cal_days_in_month(CAL_GREGORIAN, (int) $validatedMonth, (int) $validatedYear);

        return [
            'startDate' => "{$validatedYear}-{$validatedMonth}-{$validatedDay}",
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
        if (substr_count($this->date, '.') > 2) {
            throw new InvalidInputException(ErrorMessage::IncorrectDateFormat->value);
        }
    }

    private function validateYear(string $year): string
    {        
        $validatedYear = strlen($year) === 2 ? substr(date('Y'), 0, 2).$year : $year;

        if ((int) $validatedYear > (int) $this->currentYear || (int) $validatedYear < 2021) {
            throw new InvalidInputException(ErrorMessage::IncorrectDateFormat->value);
        }

        return $validatedYear;
    }

    private function validateMonth(string $month): string
    {
        $monthAsInt = (int) $month;
        
        if ($monthAsInt > self::MONTHS_IN_YEAR || $monthAsInt <= 0) {
            throw new InvalidInputException(ErrorMessage::IncorrectDateFormat->value);
        }

        return strlen($month) === 1 ? '0'.$month : $month;
    }

    private function validateDay(string $year, string $month, string $day): string
    {
        $lastDayNumberOfSpecifiedMonth = cal_days_in_month(CAL_GREGORIAN, (int) $month, (int) $year);
        $dayAsInt = (int) $day;
        
        if ($dayAsInt <= 0 || $dayAsInt > $lastDayNumberOfSpecifiedMonth) {
            throw new InvalidInputException(ErrorMessage::IncorrectDateFormat->value);
        }

        return strlen($day) === 1 ? '0'.$day : $day;
    }

    private function validateYearLenth(string $year): void
    {
        $yearLength = strlen($year);

        if ($yearLength !== 4 && $yearLength !== 2) {
            throw new InvalidInputException(ErrorMessage::IncorrectDateFormat->value);
        }
    }

    private function validateMonthLenth(string $month): void
    {
        $monthLength = strlen($month);

        if ($monthLength !== 1 && $monthLength !== 2) {
            throw new InvalidInputException(ErrorMessage::IncorrectDateFormat->value);
        }
    }

    private function validateDayLenth(string $day): void
    {
        if (strlen($day) > 2) {
            throw new InvalidInputException(ErrorMessage::IncorrectDateFormat->value);
        }
    }
}