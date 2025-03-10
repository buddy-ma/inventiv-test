<?php

namespace App\Services;

use InvalidArgumentException;

class CalculatorService
{
    /**
     * Perform a calculation based on the given operation.
     *
     * @param float $firstNumber
     * @param float $secondNumber
     * @param string $operation
     * @return float
     * @throws InvalidArgumentException
     */
    public function calculate(float $firstNumber, float $secondNumber, string $operation): float
    {
        return match ($operation) {
            'add' => $this->add($firstNumber, $secondNumber),
            'subtract' => $this->subtract($firstNumber, $secondNumber),
            'multiply' => $this->multiply($firstNumber, $secondNumber),
            'divide' => $this->divide($firstNumber, $secondNumber),
            default => throw new InvalidArgumentException("Unsupported operation: {$operation}"),
        };
    }

    /**
     * Add two numbers.
     *
     * @param float $a
     * @param float $b
     * @return float
     */
    private function add(float $a, float $b): float
    {
        return $a + $b;
    }

    /**
     * Subtract second number from first number.
     *
     * @param float $a
     * @param float $b
     * @return float
     */
    private function subtract(float $a, float $b): float
    {
        return $a - $b;
    }

    /**
     * Multiply two numbers.
     *
     * @param float $a
     * @param float $b
     * @return float
     */
    private function multiply(float $a, float $b): float
    {
        return $a * $b;
    }

    /**
     * Divide first number by second number.
     *
     * @param float $a
     * @param float $b
     * @return float
     * @throws InvalidArgumentException
     */
    private function divide(float $a, float $b): float
    {
        if ($b === 0.0) {
            throw new InvalidArgumentException('Division by zero is not allowed.');
        }

        return $a / $b;
    }
}
