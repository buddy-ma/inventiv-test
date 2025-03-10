# Calculator Application - Technical Documentation

This document provides detailed technical information about the Calculator application implementation.

## Table of Contents

1. [Architecture Overview](#architecture-overview)
2. [Code Structure](#code-structure)
3. [Controller Implementation](#controller-implementation)
4. [Livewire Implementation](#livewire-implementation)
5. [Calculator Service](#calculator-service)
6. [UI Components](#ui-components)
7. [State Management](#state-management)
8. [Routing](#routing)
9. [Error Handling](#error-handling)
10. [Extending the Application](#extending-the-application)

## Architecture Overview

The application follows a layered architecture:

```
┌─────────────────┐
│     Views       │ Blade templates & Livewire components
└────────┬────────┘
         │
┌────────▼────────┐
│  Controllers/   │ Handle user input and coordinate responses
│  Components     │
└────────┬────────┘
         │
┌────────▼────────┐
│    Services     │ Business logic for calculations
└─────────────────┘
```

This separation of concerns allows for:
- Independent testing of business logic
- Multiple UI implementations (controller-based and Livewire)
- Clear responsibility boundaries

## Code Structure

```
app/
├── Http/
│   └── Controllers/
│       └── CalculatorController.php  # Traditional controller implementation
├── Livewire/
│   └── Calculator.php                # Livewire component implementation
├── Services/
│   └── CalculatorService.php         # Shared calculation logic
resources/
├── views/
│   ├── calculator/
│   │   ├── index.blade.php           # Controller-based view
│   │   └── livewire.blade.php        # Livewire wrapper view
│   └── livewire/
│       └── calculator.blade.php      # Livewire component view
routes/
└── web.php                           # Route definitions
```

## Controller Implementation

### State Management

The controller implementation uses Laravel's session to maintain state between requests:

```php
// Initialize session variables
session(['calculator' => [
    'numberBar' => '',
    'firstNumber' => null,
    'secondNumber' => null,
    'operation' => 'add',
    'result' => null,
    'error' => null,
    'waitingForSecondNumber' => false,
]]);
```

### Key Methods

#### `addNumber(Request $request)`

Adds a digit to the current number:

```php
public function addNumber(Request $request)
{
    $number = $request->input('number');
    $calculator = session('calculator');
    
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
```

#### `setOperation(Request $request)`

Sets the operation and prepares for the second number:

```php
public function setOperation(Request $request)
{
    $operation = $request->input('operation');
    $calculator = session('calculator');
    
    if ($calculator['firstNumber'] !== null) {
        $calculator['operation'] = $operation;
        $calculator['waitingForSecondNumber'] = true;
        
        session(['calculator' => $calculator]);
    }
    
    return redirect()->route('calculator.index');
}
```

#### `calculate()`

Performs the calculation using the CalculatorService:

```php
public function calculate()
{
    $this->performCalculation();
    
    return redirect()->route('calculator.index');
}

private function performCalculation()
{
    $calculator = session('calculator');
    
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
```

## Livewire Implementation

### Component Properties

The Livewire implementation uses component properties to maintain state:

```php
class Calculator extends Component
{
    public $numberBar = '';
    public $firstNumber = null;
    public $secondNumber = null;
    public $operation = 'add';
    public $result = null;
    public $error = null;
    public $waitingForSecondNumber = false;
    
    // ...
}
```

### Key Methods

#### `addNumber($number)`

Adds a digit to the current number:

```php
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
```

#### `setOperation($operation)`

Sets the operation and prepares for the second number:

```php
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
```

#### `calculate(CalculatorService $calculatorService)`

Performs the calculation using the CalculatorService:

```php
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
```

## Calculator Service

The `CalculatorService` class contains the business logic for performing calculations:

```php
class CalculatorService
{
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
    
    private function add(float $a, float $b): float
    {
        return $a + $b;
    }
    
    private function subtract(float $a, float $b): float
    {
        return $a - $b;
    }
    
    private function multiply(float $a, float $b): float
    {
        return $a * $b;
    }
    
    private function divide(float $a, float $b): float
    {
        if ($b === 0.0) {
            throw new InvalidArgumentException('Division by zero is not allowed.');
        }
        
        return $a / $b;
    }
}
```

## UI Components

### Number Display

The calculator uses a read-only input field to display the current number or result:

```html
<input type="text" id="numberBar" value="{{ $numberBar }}" readonly
    class="bg-gray-200 dark:bg-neutral-950 shadow appearance-none border-none rounded w-full py-2 px-3 h-24 text-gray-700 dark:text-neutral-200 leading-tight font-bold text-4xl focus:outline-none focus:shadow-outline">
```

### Number Buttons

Number buttons are implemented as form submissions in the controller version:

```html
<form method="POST" action="{{ route('calculator.addNumber') }}" class="number-form">
    @csrf
    <input type="hidden" name="number" value="{{ $i }}">
    <button type="submit"
        class="bg-gray-200 dark:bg-neutral-800 hover:bg-gray-300 dark:hover:bg-neutral-700 cursor-pointer text-gray-700 h-12 dark:text-neutral-200 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
        {{ $i }}
    </button>
</form>
```

And as Livewire method calls in the Livewire version:

```html
<button type="button" wire:click="addNumber({{ $i }})"
    class="bg-gray-200 dark:bg-neutral-800 hover:bg-gray-300 dark:hover:bg-neutral-700 cursor-pointer text-gray-700 h-12 dark:text-neutral-200 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
    {{ $i }}
</button>
```

### Operation Buttons

Operation buttons follow a similar pattern, with form submissions for the controller version and Livewire method calls for the Livewire version.

## State Management

### Controller State Flow

1. User clicks a button
2. Form is submitted to the server
3. Controller method updates session state
4. Page is reloaded with updated state
5. View renders based on session state

### Livewire State Flow

1. User clicks a button
2. Livewire method is called
3. Component properties are updated
4. Component is re-rendered without page reload
5. View reflects updated component state

## Routing

The application uses the following routes:

```php
// Controller routes
Route::get('/calculator', [CalculatorController::class, 'index'])->name('calculator.index');
Route::post('/calculator/add-number', [CalculatorController::class, 'addNumber'])->name('calculator.addNumber');
Route::post('/calculator/clear-number', [CalculatorController::class, 'clearNumber'])->name('calculator.clearNumber');
Route::post('/calculator/clear-all', [CalculatorController::class, 'clearAll'])->name('calculator.clearAll');
Route::post('/calculator/set-operation', [CalculatorController::class, 'setOperation'])->name('calculator.setOperation');
Route::post('/calculator/calculate', [CalculatorController::class, 'calculate'])->name('calculator.calculate');

// Livewire route
Route::get('/calculator/livewire', function () {
    return view('calculator.livewire');
})->name('calculator.livewire');
```

## Error Handling

The application handles errors such as division by zero by:

1. Catching exceptions in the service layer
2. Storing error messages in the state (session or component property)
3. Displaying error messages in the view

```php
try {
    // Perform calculation
} catch (InvalidArgumentException $e) {
    $this->error = $e->getMessage();
}
```

## Extending the Application

### Adding New Operations

To add a new operation:

1. Add a new operation constant in the `CalculatorService` class
2. Add a new method to perform the operation
3. Update the `calculate` method to handle the new operation
4. Add a new operation button in the view

### Styling Customization

The application uses Tailwind CSS for styling. To customize the appearance:

1. Modify the CSS classes in the Blade templates
2. Use Tailwind's utility classes for quick styling changes
3. For more extensive changes, extend the Tailwind configuration

### Adding Features

Some potential features to add:

1. **Memory Functions**: Add memory store, recall, and clear functions
2. **History**: Keep a history of calculations
3. **Scientific Functions**: Add trigonometric, logarithmic, and other scientific functions
4. **Keyboard Support**: Allow keyboard input for numbers and operations
5. **Responsive Design Improvements**: Optimize for different screen sizes 
