<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CalculatorService;

class CalculatorController extends Controller
{
    /**
     * @var CalculatorService
     */
    protected $calculatorService;

    /**
     * Create a new controller instance.
     *
     * @param CalculatorService $calculatorService
     * @return void
     */
    public function __construct(CalculatorService $calculatorService)
    {
        $this->calculatorService = $calculatorService;
    }

    /**
     * Show the calculator form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Initialize session variables if they don't exist
        if (!session()->has('calculator')) {
            $this->resetCalculator();
        }

        return view('calculator.index', [
            'numberBar' => session('calculator.numberBar', ''),
            'result' => session('calculator.result'),
            'error' => session('calculator.error'),
        ]);
    }

    /**
     * Add a number to the display.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addNumber(Request $request)
    {
        $number = $request->input('number');
        $calculator = session('calculator');

        if ($calculator['result'] !== null && !$calculator['waitingForSecondNumber']) {
            // If there's a result and we're not waiting for second number,
            // start a new calculation
            $this->resetCalculator();
            $calculator = session('calculator');
        }

        if ($calculator['waitingForSecondNumber']) {
            // We're entering the second number
            $calculator['numberBar'] = $number;
            $calculator['secondNumber'] = $number;
            $calculator['waitingForSecondNumber'] = false;
        } else {
            // We're entering or continuing the first number
            $calculator['numberBar'] = $calculator['numberBar'] . $number;
            $calculator['firstNumber'] = $calculator['numberBar'];
        }

        session(['calculator' => $calculator]);

        return redirect()->route('calculator.index');
    }

    /**
     * Clear the current number.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clearNumber()
    {
        $calculator = session('calculator');

        $calculator['numberBar'] = '';
        if (!$calculator['waitingForSecondNumber']) {
            $calculator['firstNumber'] = null;
        } else {
            $calculator['secondNumber'] = null;
        }

        session(['calculator' => $calculator]);

        return redirect()->route('calculator.index');
    }

    /**
     * Reset the calculator.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clearAll()
    {
        $this->resetCalculator();

        return redirect()->route('calculator.index');
    }

    /**
     * Set the operation.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setOperation(Request $request)
    {
        $operation = $request->input('operation');
        $calculator = session('calculator');

        if ($calculator['firstNumber'] !== null) {
            // If we already have a first number and an operation in progress
            if ($calculator['secondNumber'] !== null) {
                // Calculate the current operation before setting a new one
                $this->performCalculation();
                $calculator = session('calculator');
            }

            $calculator['operation'] = $operation;
            $calculator['waitingForSecondNumber'] = true;

            session(['calculator' => $calculator]);
        }

        return redirect()->route('calculator.index');
    }

    /**
     * Process the calculation.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function calculate()
    {
        $this->performCalculation();

        return redirect()->route('calculator.index');
    }

    /**
     * Perform the calculation and update session.
     */
    private function performCalculation()
    {
        $calculator = session('calculator');
        $calculator['error'] = null;

        // If we don't have a second number yet, use the first number
        if ($calculator['secondNumber'] === null) {
            $calculator['secondNumber'] = $calculator['firstNumber'];
        }

        try {
            if ($calculator['firstNumber'] !== null && $calculator['secondNumber'] !== null) {
                $calculator['result'] = $this->calculatorService->calculate(
                    (float) $calculator['firstNumber'],
                    (float) $calculator['secondNumber'],
                    $calculator['operation']
                );

                // Display the result and prepare for next operation
                $calculator['numberBar'] = (string) $calculator['result'];
                $calculator['firstNumber'] = $calculator['result'];
                $calculator['secondNumber'] = null;
            }
        } catch (\Exception $e) {
            $calculator['error'] = $e->getMessage();
        }

        session(['calculator' => $calculator]);
    }

    /**
     * Reset calculator state in session.
     */
    private function resetCalculator()
    {
        session(['calculator' => [
            'numberBar' => '',
            'firstNumber' => null,
            'secondNumber' => null,
            'operation' => 'add',
            'result' => null,
            'error' => null,
            'waitingForSecondNumber' => false,
        ]]);
    }
}
