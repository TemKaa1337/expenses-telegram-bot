<?php declare(strict_types = 1);

namespace App\Services\Validators;

use App\Exceptions\InvalidInputException;
use App\Messages\Error;

final readonly class DateValidator
{
    private const MONTHS_IN_YEAR = 12;

    private string $currentYear;
    private string $currentMonth;
    private string $currentDay;
    private string $date;

    /**
     * @param string $date
     * @param bool $allowEmptyDate
     */
    public function __construct(
        string $date,
        private bool $allowEmptyDate
    )
    {
        $this->date = str_replace(' ', '', $date);
        [$this->currentYear, $this->currentMonth, $this->currentDay] = explode('-', date('Y-m-d'));
    }

    /**
     * @return array{startDate: string, endDate: string}
     * @throws InvalidInputException
     */
    public function validate(): array
    {
        $this->validateAllowedSymbols();
        $this->validateEmptyDate();
        $this->validateDotsCount();
        if ($this->date === '') {
            $lastDayNumberOfSpecifiedMonth = cal_days_in_month(
                CAL_GREGORIAN,
                (int) $this->currentMonth,
                (int) $this->currentYear
            );
            return [
                'startDate' => "{$this->currentYear}-{$this->currentMonth}-{$this->currentDay}",
                'endDate' => "{$this->currentYear}-{$this->currentMonth}-{$lastDayNumberOfSpecifiedMonth}"
            ];
        }

        if (!str_contains($this->date, '.')) {
            $this->validateDayLength($this->date);
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


        $this->validateDayLength($day);
        $this->validateMonthLength($month);
        $this->validateYearLength($year);

        $validatedYear = $this->validateYear($year);
        $validatedMonth = $this->validateMonth($month);
        $validatedDay = $this->validateDay($year, $validatedMonth, $day);
        $lastDayNumberOfSpecifiedMonth = cal_days_in_month(CAL_GREGORIAN, (int) $validatedMonth, (int) $validatedYear);
        return [
            'startDate' => "{$validatedYear}-{$validatedMonth}-{$validatedDay}",
            'endDate' => "{$validatedYear}-{$validatedMonth}-{$lastDayNumberOfSpecifiedMonth}"
        ];
    }

    /**
     * @return void
     * @throws InvalidInputException
     */
    private function validateAllowedSymbols(): void
    {
        if ($this->date !== '' && !preg_match("/^[.\d]+$/i", $this->date)) {
            throw new InvalidInputException(Error::IncorrectDateFormat->value);
        }
    }

    /**
     * @return void
     * @throws InvalidInputException
     */
    private function validateEmptyDate(): void
    {
        if ($this->date === '' && !$this->allowEmptyDate) {
            throw new InvalidInputException(Error::IncorrectDateFormat->value);
        }
    }

    /**
     * @return void
     * @throws InvalidInputException
     */
    private function validateDotsCount(): void
    {
        if (substr_count($this->date, '.') > 2) {
            throw new InvalidInputException(Error::IncorrectDateFormat->value);
        }
    }

    /**
     * @param string $year
     * @return string
     * @throws InvalidInputException
     */
    private function validateYear(string $year): string
    {
        $validatedYear = strlen($year) === 2 ? substr(date('Y'), 0, 2).$year : $year;
        if ((int) $validatedYear > (int) $this->currentYear || (int) $validatedYear < 2021) {
            throw new InvalidInputException(Error::IncorrectDateFormat->value);
        }
        return $validatedYear;
    }

    /**
     * @param string $month
     * @return string
     * @throws InvalidInputException
     */
    private function validateMonth(string $month): string
    {
        $monthAsInt = (int) $month;
        if ($monthAsInt > self::MONTHS_IN_YEAR || $monthAsInt <= 0) {
            throw new InvalidInputException(Error::IncorrectDateFormat->value);
        }
        return strlen($month) === 1 ? '0'.$month : $month;
    }

    /**
     * @param string $year
     * @param string $month
     * @param string $day
     * @return string
     * @throws InvalidInputException
     */
    private function validateDay(string $year, string $month, string $day): string
    {
        $lastDayNumberOfSpecifiedMonth = cal_days_in_month(CAL_GREGORIAN, (int) $month, (int) $year);
        $dayAsInt = (int) $day;
        if ($dayAsInt <= 0 || $dayAsInt > $lastDayNumberOfSpecifiedMonth) {
            throw new InvalidInputException(Error::IncorrectDateFormat->value);
        }
        return strlen($day) === 1 ? '0'.$day : $day;
    }

    /**
     * @param string $year
     * @return void
     * @throws InvalidInputException
     */
    private function validateYearLength(string $year): void
    {
        $yearLength = strlen($year);
        if ($yearLength !== 4 && $yearLength !== 2) {
            throw new InvalidInputException(Error::IncorrectDateFormat->value);
        }
    }

    /**
     * @param string $month
     * @return void
     * @throws InvalidInputException
     */
    private function validateMonthLength(string $month): void
    {
        $monthLength = strlen($month);
        if ($monthLength !== 1 && $monthLength !== 2) {
            throw new InvalidInputException(Error::IncorrectDateFormat->value);
        }
    }

    /**
     * @param string $day
     * @return void
     * @throws InvalidInputException
     */
    private function validateDayLength(string $day): void
    {
        if (strlen($day) > 2) {
            throw new InvalidInputException(Error::IncorrectDateFormat->value);
        }
    }
}