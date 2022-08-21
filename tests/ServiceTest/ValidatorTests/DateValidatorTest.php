<?php declare(strict_types=1);

use App\Exception\InvalidInputException;
use App\Services\Validator\Date\DateValidator;
use PHPUnit\Framework\TestCase;

final class DateValidatorTest extends TestCase 
{
    public function testIncorrectProvidedDateWithAllowedEmptyDate(): void
    {
        $input = '00';
        $validator = new DateValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '01.21';
        $validator = new DateValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '-1';
        $validator = new DateValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '00.2';
        $validator = new DateValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '13';
        $validator = new DateValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = strval(intval(date('m'))) + 1;
        $validator = new DateValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '2.-100';
        $validator = new DateValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '2,-100';
        $validator = new DateValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '1|';
        $validator = new DateValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '1.,21';
        $validator = new DateValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = strval(intval(date('d')) + 1).'.'.date('m').'.'.date('Y');
        $validator = new DateValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = date('d').'.'.strval(intval(date('m')) + 1).'.'.date('Y');
        $validator = new DateValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = date('d').'.'.date('m').'.'.strval(intval(date('Y')) + 1);
        $validator = new DateValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = strval(intval(date('d')) + 1).'.'.strval(intval(date('m')) + 1).'.'.date('Y');
        $validator = new DateValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = strval(intval(date('d')) + 1).'.'.date('m').'.'.strval(intval(date('Y')) + 1);
        $validator = new DateValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = date('d').'.'.strval(intval(date('m')) + 1).'.'.strval(intval(date('Y')) + 1);
        $validator = new DateValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = strval(intval(date('d')) + 1).'.'.strval(intval(date('m')) + 1).'.'.strval(intval(date('Y')) + 1);
        $validator = new DateValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $lastDayNumber = cal_days_in_month(CAL_GREGORIAN, (int) date('m'), (int) date('Y'));
        $input = strval($lastDayNumber + 1);
        $validator = new DateValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $lastDayNumber = cal_days_in_month(CAL_GREGORIAN, intval(date('m')) - 1, (int) date('Y'));
        $input = ($lastDayNumber + 1).'.'.date('m', strtotime('-1 month'));
        $validator = new DateValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $lastDayNumber = cal_days_in_month(CAL_GREGORIAN, (int) date('m'), intval(date('Y')) - 1);
        $input = ($lastDayNumber + 1).'.'.date('m').'.'.date('Y', strtotime('-1 year'));
        $validator = new DateValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = date('m').'.'.date('Y');
        $validator = new DateValidator(date: $input, allowEmptyDate: true);
        $this->expectException(InvalidInputException::class);
        $validator->validate();
    }

    public function testIncorrectProvidedDateWithoutAllowedEmptyDate(): void
    {
        $input = '';
        $validator = new DateValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '01.21';
        $validator = new DateValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '00';
        $validator = new DateValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '-1';
        $validator = new DateValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '00.2';
        $validator = new DateValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '13';
        $validator = new DateValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = strval(intval(date('m'))) + 1;
        $validator = new DateValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '2.-100';
        $validator = new DateValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '2,-100';
        $validator = new DateValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '1|';
        $validator = new DateValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = '1.,21';
        $validator = new DateValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = strval(intval(date('d')) + 1).'.'.date('m').'.'.date('Y');
        $validator = new DateValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = date('d').'.'.strval(intval(date('m')) + 1).'.'.date('Y');
        $validator = new DateValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = date('d').'.'.date('m').'.'.strval(intval(date('Y')) + 1);
        $validator = new DateValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = strval(intval(date('d')) + 1).'.'.strval(intval(date('m')) + 1).'.'.date('Y');
        $validator = new DateValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = strval(intval(date('d')) + 1).'.'.date('m').'.'.strval(intval(date('Y')) + 1);
        $validator = new DateValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = date('d').'.'.strval(intval(date('m')) + 1).'.'.strval(intval(date('Y')) + 1);
        $validator = new DateValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = strval(intval(date('d')) + 1).'.'.strval(intval(date('m')) + 1).'.'.strval(intval(date('Y')) + 1);
        $validator = new DateValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $lastDayNumber = cal_days_in_month(CAL_GREGORIAN, (int) date('m'), (int) date('Y'));
        $input = strval($lastDayNumber + 1);
        $validator = new DateValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $lastDayNumber = cal_days_in_month(CAL_GREGORIAN, intval(date('m')) - 1, (int) date('Y'));
        $input = ($lastDayNumber + 1).'.'.date('m', strtotime('-1 month'));
        $validator = new DateValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $lastDayNumber = cal_days_in_month(CAL_GREGORIAN, (int) date('m'), intval(date('Y')) - 1);
        $input = ($lastDayNumber + 1).'.'.date('m').'.'.date('Y', strtotime('-1 year'));
        $validator = new DateValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();

        $input = date('m').'.'.date('Y');
        $validator = new DateValidator(date: $input, allowEmptyDate: false);
        $this->expectException(InvalidInputException::class);
        $validator->validate();
    }
    
    public function testCorrectlyProvidedDateWithAllowedEmptyDate(): void
    {
        $input = '';
        $currentYearAndMonth = date('Y-m');
        $currentDay = date('d');
        $lastDayNumber = cal_days_in_month(CAL_GREGORIAN, (int) date('m'), (int) date('Y'));
        $validator = new DateValidator(date: $input, allowEmptyDate: true);
        $result = $validator->validate();

        $this->assertArrayHasKey('startDate', $result, 'Validated array hasnt startDate key');
        $this->assertArrayHasKey('endDate', $result, 'Validated array hasnt endDate key');
        $this->assertEquals($result['startDate'], $currentYearAndMonth.'-'.$currentDay);
        $this->assertEquals($result['endDate'], $currentYearAndMonth.'-'.$lastDayNumber);

        $input = '1';
        $currentYear = date('Y');
        $currentMonth = date('m');
        $lastDayNumber = cal_days_in_month(CAL_GREGORIAN, (int) $currentMonth, (int) $currentYear);
        $validator = new DateValidator(date: $input, allowEmptyDate: true);
        $result = $validator->validate();

        $this->assertArrayHasKey('startDate', $result, 'Validated array hasnt startDate key');
        $this->assertArrayHasKey('endDate', $result, 'Validated array hasnt endDate key');
        $this->assertEquals($result['startDate'], $currentYear.'-'.$currentMonth.'-01');
        $this->assertEquals($result['endDate'], $currentYear.'-'.$currentMonth.'-'.$lastDayNumber);

        $input = '01';
        $currentYear = date('Y');
        $currentMonth = date('m');
        $lastDayNumber = cal_days_in_month(CAL_GREGORIAN, (int) $currentMonth, (int) $currentYear);
        $validator = new DateValidator(date: $input, allowEmptyDate: true);
        $result = $validator->validate();

        $this->assertArrayHasKey('startDate', $result, 'Validated array hasnt startDate key');
        $this->assertArrayHasKey('endDate', $result, 'Validated array hasnt endDate key');
        $this->assertEquals($result['startDate'], $currentYear.'-'.$currentMonth.'-01');
        $this->assertEquals($result['endDate'], $currentYear.'-'.$currentMonth.'-'.$lastDayNumber);
        
        $input = '10.10.2021';
        $lastDayNumber = cal_days_in_month(CAL_GREGORIAN, 10, 2021);
        $validator = new DateValidator(date: $input, allowEmptyDate: true);
        $result = $validator->validate();

        $this->assertArrayHasKey('startDate', $result, 'Validated array hasnt startDate key');
        $this->assertArrayHasKey('endDate', $result, 'Validated array hasnt endDate key');
        $this->assertEquals($result['startDate'], '2021-10-10');
        $this->assertEquals($result['endDate'], '2021-10-'.$lastDayNumber);

        $input = date('d');
        $currentYear = date('Y');
        $currentMonth = date('m');
        $lastDayNumber = cal_days_in_month(CAL_GREGORIAN, (int) $currentMonth, (int) $currentYear);
        $validator = new DateValidator(date: $input, allowEmptyDate: true);
        $result = $validator->validate();

        $this->assertArrayHasKey('startDate', $result, 'Validated array hasnt startDate key');
        $this->assertArrayHasKey('endDate', $result, 'Validated array hasnt endDate key');
        $this->assertEquals($result['startDate'], $currentYear.'-'.$currentMonth.'-'.$input);
        $this->assertEquals($result['endDate'], $currentYear.'-'.$currentMonth.'-'.$lastDayNumber);
    }

    public function testCorrectlyProvidedDateWithoutAllowedEmptyDate(): void
    {
        $input = '1';
        $currentYear = date('Y');
        $currentMonth = date('m');
        $lastDayNumber = cal_days_in_month(CAL_GREGORIAN, (int) $currentMonth, (int) $currentYear);
        $validator = new DateValidator(date: $input, allowEmptyDate: false);
        $result = $validator->validate();

        $this->assertArrayHasKey('startDate', $result, 'Validated array hasnt startDate key');
        $this->assertArrayHasKey('endDate', $result, 'Validated array hasnt endDate key');
        $this->assertEquals($result['startDate'], $currentYear.'-'.$currentMonth.'-01');
        $this->assertEquals($result['endDate'], $currentYear.'-'.$currentMonth.'-'.$lastDayNumber);

        $input = '01';
        $currentYear = date('Y');
        $currentMonth = date('m');
        $lastDayNumber = cal_days_in_month(CAL_GREGORIAN, (int) $currentMonth, (int) $currentYear);
        $validator = new DateValidator(date: $input, allowEmptyDate: false);
        $result = $validator->validate();

        $this->assertArrayHasKey('startDate', $result, 'Validated array hasnt startDate key');
        $this->assertArrayHasKey('endDate', $result, 'Validated array hasnt endDate key');
        $this->assertEquals($result['startDate'], $currentYear.'-'.$currentMonth.'-01');
        $this->assertEquals($result['endDate'], $currentYear.'-'.$currentMonth.'-'.$lastDayNumber);
        
        $input = '10.10.2021';
        $lastDayNumber = cal_days_in_month(CAL_GREGORIAN, 10, 2021);
        $validator = new DateValidator(date: $input, allowEmptyDate: false);
        $result = $validator->validate();

        $this->assertArrayHasKey('startDate', $result, 'Validated array hasnt startDate key');
        $this->assertArrayHasKey('endDate', $result, 'Validated array hasnt endDate key');
        $this->assertEquals($result['startDate'], '2021-10-10');
        $this->assertEquals($result['endDate'], '2021-10-'.$lastDayNumber);

        $input = date('d');
        $currentYear = date('Y');
        $currentMonth = date('m');
        $lastDayNumber = cal_days_in_month(CAL_GREGORIAN, (int) $currentMonth, (int) $currentYear);
        $validator = new DateValidator(date: $input, allowEmptyDate: false);
        $result = $validator->validate();

        $this->assertArrayHasKey('startDate', $result, 'Validated array hasnt startDate key');
        $this->assertArrayHasKey('endDate', $result, 'Validated array hasnt endDate key');
        $this->assertEquals($result['startDate'], $currentYear.'-'.$currentMonth.'-'.$input);
        $this->assertEquals($result['endDate'], $currentYear.'-'.$currentMonth.'-'.$lastDayNumber);
    }
}