<?php

namespace App\Livewire;

use App\Services\CalculatorService;
use InvalidArgumentException;
use Livewire\Component;

class Calculator extends Component
{
    public $numberBar = '';
    public $firstNumber = null;
    public $secondNumber = null;
    public $operation = 'add';
    public $result = null;
    public $error = null;
    public $waitingForSecondNumber = false;

    protected $rules = [
        'firstNumber' => 'required|numeric',
        'secondNumber' => 'required|numeric',
        'operation' => 'required|in:add,subtract,multiply,divide',
    ];

    public function addNumber($number)
    {
        if ($this->result !== null && !$this->waitingForSecondNumber) {
            // If there's a result and we're not waiting for second number,
            // start a new calculation
            $this->clearAll();
        }

        if ($this->waitingForSecondNumber) {
            // We're entering the second number
            $this->numberBar = $number;
            $this->secondNumber = $number;
            $this->waitingForSecondNumber = false;
        } else {
            // We're entering or continuing the first number
            $this->numberBar = $this->numberBar . $number;
            $this->firstNumber = $this->numberBar;
        }
    }

    public function clearNumber()
    {
        $this->numberBar = '';
        if (!$this->waitingForSecondNumber) {
            $this->firstNumber = null;
        } else {
            $this->secondNumber = null;
        }
    }

    public function clearAll()
    {
        $this->numberBar = '';
        $this->firstNumber = null;
        $this->secondNumber = null;
        $this->operation = 'add';
        $this->result = null;
        $this->error = null;
        $this->waitingForSecondNumber = false;
    }

    public function calculate(CalculatorService $calculatorService)
    {
        $this->error = null;

        // If we don't have a second number yet, use the first number
        if ($this->secondNumber === null) {
            $this->secondNumber = $this->firstNumber;
        }

        try {
            if ($this->firstNumber !== null && $this->secondNumber !== null) {
                $this->result = $calculatorService->calculate(
                    (float) $this->firstNumber,
                    (float) $this->secondNumber,
                    $this->operation
                );

                // Display the result and prepare for next operation
                $this->numberBar = (string) $this->result;
                $this->firstNumber = $this->result;
                $this->secondNumber = null;
            }
        } catch (InvalidArgumentException $e) {
            $this->error = $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.calculator');
    }

    public function setOperation($operation)
    {
        if ($this->firstNumber !== null) {
            // If we already have a first number and an operation in progress
            if ($this->secondNumber !== null) {
                // Calculate the current operation before setting a new one
                $this->calculate(app(CalculatorService::class));
            }

            $this->operation = $operation;
            $this->waitingForSecondNumber = true;
        }
    }
}
