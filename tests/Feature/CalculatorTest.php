<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculatorTest extends TestCase
{
    public function testCalculatorPageLoads(): void
    {
        $response = $this->get(route('calculator.index'));
        $response->assertStatus(200);
        $response->assertViewIs('calculator.index');
    }

    public function testAdditionCalculation(): void
    {
        $response = $this->post(route('calculator.calculate'), [
            'first_number' => 5,
            'second_number' => 3,
            'operation' => 'add',
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('calculator.index');
        $response->assertViewHas('result', 8);
    }

    public function testSubtractionCalculation(): void
    {
        $response = $this->post(route('calculator.calculate'), [
            'first_number' => 5,
            'second_number' => 3,
            'operation' => 'subtract',
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('calculator.index');
        $response->assertViewHas('result', 2);
    }

    public function testMultiplicationCalculation(): void
    {
        $response = $this->post(route('calculator.calculate'), [
            'first_number' => 5,
            'second_number' => 3,
            'operation' => 'multiply',
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('calculator.index');
        $response->assertViewHas('result', 15);
    }

    public function testDivisionCalculation(): void
    {
        $response = $this->post(route('calculator.calculate'), [
            'first_number' => 6,
            'second_number' => 3,
            'operation' => 'divide',
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('calculator.index');
        $response->assertViewHas('result', 2);
    }

    public function testDivisionByZeroError(): void
    {
        $response = $this->post(route('calculator.calculate'), [
            'first_number' => 5,
            'second_number' => 0,
            'operation' => 'divide',
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('calculator.index');
        $response->assertViewHas('error', 'Division by zero is not allowed.');
    }

    public function testValidationErrors(): void
    {
        $response = $this->post(route('calculator.calculate'), [
            'first_number' => 'not-a-number',
            'second_number' => 3,
            'operation' => 'add',
        ]);

        $response->assertStatus(302); // Redirect due to validation error
        $response->assertSessionHasErrors('first_number');
    }
}
