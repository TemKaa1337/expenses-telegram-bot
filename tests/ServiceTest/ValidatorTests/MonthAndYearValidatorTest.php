<?php declare(strict_types=1);

use App\Exception\InvalidInputException;
use App\Services\Validator\Date\MonthAndYearValidator;
use PHPUnit\Framework\TestCase;

final class MonthAndYearValidatorTest extends TestCase 
{
    public function testIncorrectProvidedDateWithAllowedEmptyDate(): void
    {
        $input = '00';
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '-1';
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '00.2';
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '13';
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = strval(intval(date('m'))) + 1;
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '01.'.strval(intval(date('Y')) + 1);
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = strval(intval(date('m')) + 1).'.'.strval(intval(date('Y')) + 1);
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = date('m').'.2020';
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '01.-2';
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '00.0';
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '123.123';
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '2.-100';
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '2,-100';
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '1|';
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '1.,21';
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();
    }

    public function testIncorrectProvidedDateWithoutAllowedEmptyDate(): void
    {
        $input = '00';
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '-1';
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '00.2';
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '13';
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = strval(intval(date('m'))) + 1;
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '01.'.strval(intval(date('Y')) + 1);
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = strval(intval(date('m')) + 1).'.'.strval(intval(date('Y')) + 1);
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = date('m').'.2020';
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '01.-2';
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '00.0';
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '123.123';
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '2.-100';
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '2,-100';
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '1|';
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '1.,21';
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();
    }

    public function testCorrectlyProvidedDateWithAllowedEmptyDate(): void
    {
        $input = '';
        $currentYearAndMonth = date('Y-m');
        $lastDayNumber = cal_days_in_month(CAL_GREGORIAN, (int) date('m'), (int) date('Y'));
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: true);
        $result = $validator->validate();

        $this->assertArrayHasKey('startDate', $result, 'Validated array hasnt startDate key');
        $this->assertArrayHasKey('endDate', $result, 'Validated array hasnt endDate key');
        $this->assertEquals($result['startDate'], $currentYearAndMonth.'-01');
        $this->assertEquals($result['endDate'], $currentYearAndMonth.'-'.$lastDayNumber);

        $input = '1';
        $currentYear = date('Y');
        $lastDayNumber = cal_days_in_month(CAL_GREGORIAN, 1, (int) date('Y'));
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: true);
        $result = $validator->validate();

        $this->assertArrayHasKey('startDate', $result, 'Validated array hasnt startDate key');
        $this->assertArrayHasKey('endDate', $result, 'Validated array hasnt endDate key');
        $this->assertEquals($result['startDate'], $currentYear.'-01-01');
        $this->assertEquals($result['endDate'], $currentYear.'-01-'.$lastDayNumber);

        $input = '01';
        $currentYear = date('Y');
        $lastDayNumber = cal_days_in_month(CAL_GREGORIAN, 1, (int) date('Y'));
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: true);
        $result = $validator->validate();

        $this->assertArrayHasKey('startDate', $result, 'Validated array hasnt startDate key');
        $this->assertArrayHasKey('endDate', $result, 'Validated array hasnt endDate key');
        $this->assertEquals($result['startDate'], $currentYear.'-01-01');
        $this->assertEquals($result['endDate'], $currentYear.'-01-'.$lastDayNumber);
        
        $input = '01.21';
        $lastDayNumber = cal_days_in_month(CAL_GREGORIAN, 1, 2021);
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: true);
        $result = $validator->validate();

        $this->assertArrayHasKey('startDate', $result, 'Validated array hasnt startDate key');
        $this->assertArrayHasKey('endDate', $result, 'Validated array hasnt endDate key');
        $this->assertEquals($result['startDate'], '2021-01-01');
        $this->assertEquals($result['endDate'], '2021-01-'.$lastDayNumber);

        $input = '4.2021';
        $lastDayNumber = cal_days_in_month(CAL_GREGORIAN, 4, 2021);
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: true);
        $result = $validator->validate();

        $this->assertArrayHasKey('startDate', $result, 'Validated array hasnt startDate key');
        $this->assertArrayHasKey('endDate', $result, 'Validated array hasnt endDate key');
        $this->assertEquals($result['startDate'], '2021-04-01');
        $this->assertEquals($result['endDate'], '2021-04-'.$lastDayNumber);
    }

    public function testCorrectlyProvidedDateWithoutAllowedEmptyDate(): void
    {
        $input = '1';
        $currentYear = date('Y');
        $lastDayNumber = cal_days_in_month(CAL_GREGORIAN, 1, (int) date('Y'));
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: false);
        $result = $validator->validate();

        $this->assertArrayHasKey('startDate', $result, 'Validated array hasnt startDate key');
        $this->assertArrayHasKey('endDate', $result, 'Validated array hasnt endDate key');
        $this->assertEquals($result['startDate'], $currentYear.'-01-01');
        $this->assertEquals($result['endDate'], $currentYear.'-01-'.$lastDayNumber);

        $input = '01';
        $currentYear = date('Y');
        $lastDayNumber = cal_days_in_month(CAL_GREGORIAN, 1, (int) date('Y'));
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: false);
        $result = $validator->validate();

        $this->assertArrayHasKey('startDate', $result, 'Validated array hasnt startDate key');
        $this->assertArrayHasKey('endDate', $result, 'Validated array hasnt endDate key');
        $this->assertEquals($result['startDate'], $currentYear.'-01-01');
        $this->assertEquals($result['endDate'], $currentYear.'-01-'.$lastDayNumber);
        
        $input = '01.21';
        $lastDayNumber = cal_days_in_month(CAL_GREGORIAN, 1, 2021);
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: false);
        $result = $validator->validate();

        $this->assertArrayHasKey('startDate', $result, 'Validated array hasnt startDate key');
        $this->assertArrayHasKey('endDate', $result, 'Validated array hasnt endDate key');
        $this->assertEquals($result['startDate'], '2021-01-01');
        $this->assertEquals($result['endDate'], '2021-01-'.$lastDayNumber);

        $input = '4.2021';
        $lastDayNumber = cal_days_in_month(CAL_GREGORIAN, 4, 2021);
        $validator = new MonthAndYearValidator(date: $input, allowEmptyDate: false);
        $result = $validator->validate();

        $this->assertArrayHasKey('startDate', $result, 'Validated array hasnt startDate key');
        $this->assertArrayHasKey('endDate', $result, 'Validated array hasnt endDate key');
        $this->assertEquals($result['startDate'], '2021-04-01');
        $this->assertEquals($result['endDate'], '2021-04-'.$lastDayNumber);
    }
}