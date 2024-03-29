<?php declare(strict_types=1);

namespace App\Services\Validators;

use App\Exceptions\InvalidInputException;
use App\Messages\Error;

final readonly class DatePartValidator
{
    private const MONTHS_IN_YEAR = 12;

    private string $currentYear;
    private string $currentMonth;
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
        [$this->currentYear, $this->currentMonth] = explode('-', date('Y-m'));
    }

    /**
     * @return string[]
     * @throws InvalidInputException
     */
    public function validate(): array
    {
        $this->validateAllowedSymbols();
        $this->validateEmptyDate();
        $this->validateDotsCount();

        if ($this->date === '') {
            $lastDayNumberOfSpecifiedMonth = cal_days_in_month(CAL_GREGORIAN,
                (int) $this->currentMonth,
                (int) $this->currentYear
            );
            return [
                'startDate' => "{$this->currentYear}-{$this->currentMonth}-01",
                'endDate' => "{$this->currentYear}-{$this->currentMonth}-{$lastDayNumberOfSpecifiedMonth}"
            ];
        }

        if (!str_contains($this->date, '.')) {
            $this->validateMonthLength($this->date);
            $validatedMonth = $this->validateMonth($this->date);
            $lastDayNumberOfSpecifiedMonth = cal_days_in_month(
                CAL_GREGORIAN,
                (int) $validatedMonth,
                (int) $this->currentYear
            );
            return [
                'startDate' => "{$this->currentYear}-{$validatedMonth}-01",
                'endDate' => "{$this->currentYear}-{$validatedMonth}-{$lastDayNumberOfSpecifiedMonth}"
            ];
        }

        [$month, $year] = explode('.', $this->date);
        $this->validateMonthLength($month);
        $this->validateYearLength($year);
        $validatedMonth = $this->validateMonth($month);
        $validatedYear = $this->validateYear($year);

        $lastDayNumberOfSpecifiedMonth = cal_days_in_month(
            CAL_GREGORIAN,
            (int) $validatedMonth,
            (int) $validatedYear
        );
        return [
            'startDate' => "{$validatedYear}-{$validatedMonth}-01",
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
        if (substr_count($this->date, '.') > 1) {
            throw new InvalidInputException(Error::IncorrectDateFormat->value);
        }
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
}