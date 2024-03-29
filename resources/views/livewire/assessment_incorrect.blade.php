<div class="flex flex-col" x-data="">
        @if (isset($incorrectAnswers[$index]) && $assessment)
            <div class="p-4">
                <h1 class="text-center text-lg font-bold">Section {{ $incorrectAnswers[$index]->section + 1 }}  - Question {{ $incorrectAnswers[$index]->question + 1 }}</h1>
            </div>
            <div class="p-4 m-auto md:w-[700px]">
                <div class="md:h-[250px] my-4">
                    @if (isset($assessment[$incorrectAnswers[$index]->section][$incorrectAnswers[$index]->question]['image']))
                        <img src="../../../../images/{{ $assessment[$incorrectAnswers[$index]->section][$incorrectAnswers[$index]->question]['image'] }}" alt="Assessment image" class="max-h-[250px] md:h-[250px] mx-auto">
                    @endif
                </div>

                <h1 class="text-2xl">{{ $assessment[$incorrectAnswers[$index]->section][$incorrectAnswers[$index]->question]['question'] }}</h1>

                <div class="flex gap-4 flex-col my-12">
                    @foreach ($assessment[$incorrectAnswers[$index]->section][$incorrectAnswers[$index]->question]['answers'] as $key => $answer)
                        <div>
                            <input class="peer hidden" type="radio" name="option" value="{{ $key }}" />
                            <label class="block w-full cursor-pointer select-none rounded-xl p-2 text-center border-solid border border-slate-800" :class="[
                                {{ $assessment[$incorrectAnswers[$index]->section][$incorrectAnswers[$index]->question]['correct'] }} === {{ $key }} ? 'bg-green-500 text-white' : '',
                                {{ $incorrectAnswers[$index]->is_correct }} === 0 && {{ $incorrectAnswers[$index]->is_correct }} === {{ $key }} ? 'bg-red-500 text-white' : ''
                            ]">
                                {{ $answer }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="flex gap-6 justify-center mt-auto p-8 w-dvw">
                <button class="text-white font-bold w-32 py-2 px-4 my-auto rounded {{ ($index === 0) ? 'bg-blue-300' : 'bg-blue-500 hover:bg-blue-700' }}" {{ ($index === 0) ? 'disabled' : '' }} wire:click="handlePreviousQuestion()">
                    Previous
                </button>
                <button class="text-white font-bold w-32 py-2 px-4 my-auto rounded {{ ($index === $count) ? 'bg-blue-300' : 'bg-blue-500 hover:bg-blue-700' }}" {{ ($index === $count) ? 'disabled' : '' }} wire:click="handleNextQuestion()">
                    Next
                </button>
            </div>
        @else
            No content
        @endif
    </div>