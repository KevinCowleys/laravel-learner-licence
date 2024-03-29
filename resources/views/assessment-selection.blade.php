<x-app-layout>
    <div class="flex flex-col justify-center items-center w-full" x-data="{ isTestSelection: false, testType: 2, testSelected: 1 }">
        <div id="type-selection" class="flex flex-col gap-6 mt-auto" x-show="!isTestSelection">
            <input id="code-1" class="peer hidden" type="radio" name="option" value="1" disabled />
            <label for="code-1" class="block w-64 cursor-pointer select-none rounded-xl p-2 text-center border-solid border border-slate-800" :class="[testType == 1 ? 'bg-blue-500 font-bold text-white' : '']">
                Code 1: Motorcycle
            </label>
            <input id="code-2" class="peer hidden" type="radio" name="option" value="2" checked />
            <label for="code-2" class="block w-64 cursor-pointer select-none rounded-xl p-2 text-center border-solid border border-slate-800" :class="[testType == 2 ? 'bg-blue-500 font-bold text-white' : '']" @click="isTestSelection = true; testType = 2">
                Code 2: Light Motor Vehicle
            </label>
            <input id="code-3" class="peer hidden" type="radio" name="option" value="1" disabled />
            <label for="code-3" class="block w-64 cursor-pointer select-none rounded-xl p-2 text-center border-solid border border-slate-800" :class="[testType == 3 ? 'bg-blue-500 font-bold text-white' : '']">
                Code 3: Heavy Motor Vehicle
            </label>
        </div>
        <div id="test-selection" class="flex flex-col gap-6 mt-auto" x-cloak x-show="isTestSelection">
            <input id="test-1" class="peer hidden" type="radio" name="option" value="1" checked />
            <label for="test-1" class="block w-64 cursor-pointer select-none rounded-xl p-2 text-center border-solid border border-slate-800" :class="[testSelected == 1 ? 'bg-blue-500 font-bold text-white' : '']" @click="testSelected = 1">
                Test 1
            </label>
            <input id="test-2" class="peer hidden" type="radio" name="option" value="2" disabled />
            <label for="test-2" class="block w-64 cursor-pointer select-none rounded-xl p-2 text-center border-solid border border-slate-800" :class="[testSelected == 2 ? 'bg-blue-500 font-bold text-white' : '']">
                Test 2
            </label>
        </div>
        <div class="flex p-8 gap-6 mt-auto">
            <button class="text-white font-bold w-32 py-2 px-4 rounded" :class="{'bg-blue-500 hover:bg-blue-700' : isTestSelection, 'bg-blue-300' : !isTestSelection}" x-bind:disabled="!isTestSelection" @click="isTestSelection = false">
                Back
            </button>
            <button class="text-white font-bold w-32 py-2 px-4 my-auto bg-blue-500 hover:bg-blue-700 rounded" x-show="!isTestSelection" @click="isTestSelection = true">
                Next
            </button>
            <button class="text-white font-bold w-32 py-2 px-4 my-auto bg-blue-500 hover:bg-blue-700 rounded" x-cloak x-show="isTestSelection" x-bind:disabled="!isTestSelection" @click="window.location.replace('/assessment/' + testType + '/' + testSelected)">
                Create Test
            </button>
        </div>
    </div>
</x-app-layout>