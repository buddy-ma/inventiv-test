<?php

namespace Tests\Unit\Services;

use App\Services\CalculatorService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CalculatorServiceTest extends TestCase
{
    private CalculatorService $calculatorService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculatorService = new CalculatorService();
    }

    public function testAddition(): void
    {
        $result = $this->calculatorService->calculate(5, 3, 'add');
        $this->assertEquals(8, $result);
    }

    public function testSubtraction(): void
    {
        $result = $this->calculatorService->calculate(5, 3, 'subtract');
        $this->assertEquals(2, $result);
    }

    public function testMultiplication(): void
    {
        $result = $this->calculatorService->calculate(5, 3, 'multiply');
        $this->assertEquals(15, $result);
    }

    public function testDivision(): void
    {
        $result = $this->calculatorService->calculate(6, 3, 'divide');
        $this->assertEquals(2, $result);
    }

    public function testDivisionByZero(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Division by zero is not allowed.');
        $this->calculatorService->calculate(5, 0, 'divide');
    }

    public function testInvalidOperation(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported operation: invalid');
        $this->calculatorService->calculate(5, 3, 'invalid');
    }
}
