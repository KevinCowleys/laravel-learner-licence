<x-app-layout>
    <div class="flex flex-col m-auto">
        <h2 class="text-2xl underline">RESULTS</h2>
        <div>Time to complete: {{ $timeSpent }}</div>
        <div class="py-4">
            @foreach ($assessmentResult as $index => $section)
                <div class="text-xl">
                    {{ $section['name'] }}: <span class="{{ $section['hasPassed'] ? 'text-green-600' : 'text-red-600' }}">{{ $section['correct'] }}</span>/{{ $section['count'] }} <span class="{{ $section['hasPassed'] ? 'text-green-600' : 'text-red-600' }}">{{ $section['hasPassed'] ? 'Pass' : 'Fail' }}</span>
                </div>
                <div class="text-gray-500">
                    Pass mark {{ $section['passMark'] }}/{{ $section['count'] }}
                </div>
            @endforeach
        </div>
        <h1 class="text-6xl font-bold mb-4 {{ $testPassed ? 'text-green-600' : 'text-red-600'}}">Test {{ $testPassed ? 'Passed' : 'Failed'}}</h1>
        <div class="flex flex-col" x-data="">
            <button class="text-white font-bold w-64 py-2 px-4 my-auto mb-4 bg-blue-500 hover:bg-blue-700 rounded m-auto" @click="window.location.replace('/')">
                Home
            </button>
            <button class="text-white font-bold w-64 py-2 px-4 my-auto bg-blue-500 hover:bg-blue-700 rounded m-auto" @click="window.location.replace('{{ $incorrectHref }}')">
                View Incorrect Answers
            </button>
        </div>
    </div>
</x-app-layout>