# Calculator Application

A simple yet powerful calculator application built with Laravel, featuring both a traditional controller-based implementation and a reactive Livewire implementation.

![Calculator Screenshot](screenshot.png)

## Features

- Basic arithmetic operations (addition, subtraction, multiplication, division)
- Clean, responsive UI with dark mode support
- Two implementation approaches:
  - Traditional controller-based with Blade templates
  - Reactive UI with Livewire components
- Error handling for invalid operations (e.g., division by zero)
- Session-based state management for the controller version
- Real-time updates for the Livewire version

## Implementation Details

### Controller-Based Implementation

The traditional implementation uses Laravel controllers, Blade templates, and session management:

- **State Management**: Uses Laravel session to maintain calculator state between requests
- **User Interaction**: Each button click submits a form to the server
- **Controller Methods**:
  - `addNumber()`: Adds a digit to the current number
  - `clearNumber()`: Clears the current number
  - `clearAll()`: Resets the calculator
  - `setOperation()`: Sets the operation (add, subtract, multiply, divide)
  - `calculate()`: Performs the calculation
- **Routing**: Uses standard Laravel routes for each action

### Livewire Implementation

The Livewire implementation provides a more reactive experience:

- **State Management**: Uses Livewire component properties to maintain state
- **User Interaction**: Real-time updates without full page reloads
- **Component Methods**:
  - `addNumber()`: Adds a digit to the current number
  - `clearNumber()`: Clears the current number
  - `clearAll()`: Resets the calculator
  - `setOperation()`: Sets the operation (add, subtract, multiply, divide)
  - `calculate()`: Performs the calculation
- **Reactivity**: UI updates instantly when component properties change

## Architecture

The application follows a clean architecture pattern:

- **Service Layer**: `CalculatorService` handles the actual calculation logic
- **Presentation Layer**: Controllers and Livewire components handle user interaction
- **View Layer**: Blade templates render the UI

## Technical Requirements

- PHP 8.1+
- Laravel 10+
- Livewire 3+
- Tailwind CSS

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/calculator-app.git
   cd calculator-app
   ```

2. Install dependencies:
   ```bash
   composer install
   npm install
   ```

3. Copy the environment file:
   ```bash
   cp .env.example .env
   ```

4. Generate application key:
   ```bash
   php artisan key:generate
   ```

5. Compile assets:
   ```bash
   npm run dev
   ```

6. Start the development server:
   ```bash
   php artisan serve
   ```

7. Visit `http://localhost:8000/calculator` for the controller version or `http://localhost:8000/calculator/livewire` for the Livewire version.

## Usage

1. Enter the first number using the number buttons
2. Select an operation (add, subtract, multiply, divide)
3. Enter the second number
4. Click the equals button to see the result
5. The result can be used for further calculations

## Comparison of Implementations

### Controller-Based Advantages
- Familiar MVC pattern
- Works without JavaScript
- Simpler debugging (standard HTTP request/response cycle)

### Livewire Advantages
- More responsive user experience
- Less code required
- No page refreshes
- Simpler state management

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT). 
