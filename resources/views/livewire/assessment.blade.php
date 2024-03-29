<div class="flex flex-col flex-1" x-data="{ selectedAnswer: {{ $selectedAnswer ?? 0 }}, timeLeft: true }">
    @if (isset($startIndex) && $assessment)
        <div class="p-4">
            <h1 class="text-center text-lg font-bold">Section {{ $section + 1 }}  - Question {{ $startIndex + 1 }}</h1>
            <span id="time-end" hidden>{{ $timeEnd }}</span>
            <div class="mx-auto md:w-[700px]">
                Time Remaining: <span id="remaining-time">{{ $timeLeft }}</span>
            </div>
            @script
            <script>
                let timeDisplay = document.getElementById('remaining-time');
                let endTime = document.getElementById('time-end').textContent;
                let timeEnd = parseInt(new Date(endTime).getTime().toLocaleString().replace(/\s/g, ''));

                // Run first time now
                updateTime();
                // Setup for every second
                let updateTimeInterval = setInterval(updateTime, 1000);

                function updateTime() {
                    let timeNow = parseInt(new Date().getTime().toLocaleString().replace(/\s/g, ''));
            
                    if (timeDisplay) {
                        let difference = timeEnd - timeNow;
                        let minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
                        let seconds = Math.floor((difference % (1000 * 60)) / 1000);

                        timeDisplay.textContent  = minutes + 'm ' + seconds + 's ';

                        if (minutes === 0 && seconds === 0) {
                            clearInterval(updateTimeInterval);
                            $wire.dispatchSelf('test-expired');
                        }
                    }
                }
            </script>
            @endscript
        </div>
        <div class="p-4 m-auto md:w-[700px]">
            <div class="md:h-[250px] my-4">
                @if (isset($assessment[$section][$startIndex]['image']))
                    <img src="../../../images/{{ $assessment[$section][$startIndex]['image'] }}" alt="Assessment image" class="max-h-[250px] md:h-[250px] mx-auto">
                @endif
            </div>

            <h1 class="text-2xl">{{ $assessment[$section][$startIndex]['question'] }}</h1>

            <div class="flex gap-4 flex-col my-12">
                @foreach ($assessment[$section][$startIndex]['answers'] as $key => $answer)
                    <div>
                        <input class="peer hidden" type="radio" name="option" value="{{ $key }}" />
                        <label class="block w-full cursor-pointer select-none rounded-xl p-2 text-center border-solid border border-slate-800" :class="[selectedAnswer === {{ $key }} ? 'bg-blue-500 text-white' : '']" @click="selectedAnswer = {{ $key }}">
                            {{ $answer }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="flex gap-6 justify-center mt-auto p-8 w-dvw">
            <button class="text-white font-bold w-32 py-2 px-4 my-auto rounded {{ ($startIndex === 0 && $section === 0) ? 'bg-blue-300' : 'bg-blue-500 hover:bg-blue-700' }}" {{ ($startIndex === 0 && $section === 0) ? 'disabled' : '' }} wire:click="handlePreviousQuestion">
                Previous
            </button>
            <button class="text-white font-bold w-32 py-2 px-4 my-auto bg-blue-500 hover:bg-blue-700 rounded" wire:click="handleNextQuestion(selectedAnswer)">
                Next
            </button>
        </div>
    @else
        No content
    @endif
</div>
