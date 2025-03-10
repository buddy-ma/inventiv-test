<x-layouts.app>
    <div class="py-12">
        <div class="bg-white dark:bg-neutral-900 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white dark:bg-neutral-900 border-b border-gray-200 dark:border-neutral-700">
                <h2 class="text-2xl font-bold mb-4">Simple Calculator
                    <div class="loader float-right ml-auto" id="loading" style="display: none;">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                    </div>
                </h2>
                <div class="mb-4">
                    <a href="{{ route('calculator.livewire') }}" class="text-blue-500 hover:text-blue-700">Try the Livewire version</a>
                </div>

                @if (isset($error))
                    <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-4"
                        role="alert">
                        <strong class="font-bold">Error:</strong>
                        <span class="block sm:inline">{{ $error }}</span>
                    </div>
                @endif

                @if (isset($result))
                    <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-4"
                        role="alert">
                        <strong class="font-bold">Result:</strong>
                        <span class="block sm:inline">{{ $result }}</span>
                    </div>
                @endif

                <div class="mb-4">
                    <div class="flex mb-2">
                        <input type="text" id="numberBar" value="{{ $numberBar }}" readonly
                            class="bg-gray-200 dark:bg-neutral-950 shadow appearance-none border-none rounded w-full py-2 px-3 h-24 text-gray-700 dark:text-neutral-200 leading-tight font-bold text-4xl focus:outline-none focus:shadow-outline">

                        <form id="calculateForm" method="POST" action="{{ route('calculator.calculate') }}" class="ml-2">
                            @csrf
                            <button type="submit"
                                class="bg-indigo-500 dark:bg-indigo-700 hover:bg-indigo-600 dark:hover:bg-indigo-800 text-white font-bold py-2 px-10 cursor-pointer rounded focus:outline-none focus:shadow-outline h-24">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-equal"><line x1="5" x2="19" y1="9" y2="9"/><line x1="5" x2="19" y1="15" y2="15"/></svg>
                            </button>
                        </form>

                        <form id="clearAllForm" method="POST" action="{{ route('calculator.clearAll') }}" class="ml-2">
                            @csrf
                            <button type="submit"
                                class="bg-red-500 dark:bg-red-700 hover:bg-red-600 dark:hover:bg-red-800 text-white font-bold py-2 px-10 cursor-pointer rounded focus:outline-none focus:shadow-outline h-24">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-delete"><path d="M10 5a2 2 0 0 0-1.344.519l-6.328 5.74a1 1 0 0 0 0 1.481l6.328 5.741A2 2 0 0 0 10 19h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2z"/><path d="m12 9 6 6"/><path d="m18 9-6 6"/></svg>
                            </button>
                        </form>
                    </div>
                    <div class="flex">
                        <div class="w-full">
                            <div class="grid grid-cols-3 gap-2">
                                @for ($i = 1; $i <= 9; $i++)
                                    <form method="POST" action="{{ route('calculator.addNumber') }}" class="number-form">
                                        @csrf
                                        <input type="hidden" name="number" value="{{ $i }}">
                                        <button type="submit"
                                            class="bg-gray-200 dark:bg-neutral-800 hover:bg-gray-300 dark:hover:bg-neutral-700 cursor-pointer text-gray-700 h-12 dark:text-neutral-200 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                                            {{ $i }}
                                        </button>
                                    </form>
                                @endfor
                            </div>
                            <form method="POST" action="{{ route('calculator.addNumber') }}" class="number-form mt-2">
                                @csrf
                                <input type="hidden" name="number" value="0">
                                <button type="submit"
                                    class="bg-gray-200 dark:bg-neutral-800 h-12 text-gray-700 dark:text-neutral-200 font-bold py-2 px-4 w-full rounded focus:outline-none focus:shadow-outline">
                                    0
                                </button>
                            </form>
                        </div>
                        <div class="flex flex-col gap-2">
                            <form method="POST" action="{{ route('calculator.setOperation') }}" class="operation-form">
                                @csrf
                                <input type="hidden" name="operation" value="add">
                                <button type="submit"
                                    class="ml-2 bg-white dark:bg-neutral-950 h-12 hover:bg-neutral-200 dark:hover:bg-neutral-800 text-gray-700 dark:text-neutral-200 font-bold py-2 px-10 cursor-pointer rounded focus:outline-none focus:shadow-outline">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('calculator.setOperation') }}" class="operation-form">
                                @csrf
                                <input type="hidden" name="operation" value="subtract">
                                <button type="submit"
                                    class="ml-2 bg-white dark:bg-neutral-950 h-12 hover:bg-neutral-200 dark:hover:bg-neutral-800 text-gray-700 dark:text-neutral-200 font-bold py-2 px-10 cursor-pointer rounded focus:outline-none focus:shadow-outline">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-minus"><path d="M5 12h14"/></svg>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('calculator.setOperation') }}" class="operation-form">
                                @csrf
                                <input type="hidden" name="operation" value="multiply">
                                <button type="submit"
                                    class="ml-2 bg-white dark:bg-neutral-950 h-12 hover:bg-neutral-200 dark:hover:bg-neutral-800 text-gray-700 dark:text-neutral-200 font-bold py-2 px-10 cursor-pointer rounded focus:outline-none focus:shadow-outline">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('calculator.setOperation') }}" class="operation-form">
                                @csrf
                                <input type="hidden" name="operation" value="divide">
                                <button type="submit"
                                    class="ml-2 bg-white dark:bg-neutral-950 h-12 hover:bg-neutral-200 dark:hover:bg-neutral-800 text-gray-700 dark:text-neutral-200 font-bold py-2 px-10 cursor-pointer rounded focus:outline-none focus:shadow-outline">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-divide"><circle cx="12" cy="6" r="1"/><line x1="5" x2="19" y1="12" y2="12"/><circle cx="12" cy="18" r="1"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add loading indicator for form submissions
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            const loading = document.getElementById('loading');

            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    loading.style.display = 'block';
                });
            });
        });
    </script>
</x-layouts.app>
