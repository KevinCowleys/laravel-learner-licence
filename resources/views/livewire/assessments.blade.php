

<div class="relative overflow-x-auto shadow-md sm:rounded-lg m-auto">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 md:min-w-[90vw]">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-zinc-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">Date</th>
                <th scope="col" class="px-6 py-3 hidden md:table-cell">Test Type</th>
                <th scope="col" class="px-6 py-3 hidden md:table-cell">Test Number</th>
                <th scope="col" class="px-6 py-3 hidden md:table-cell">Section 1</th>
                <th scope="col" class="px-6 py-3 hidden md:table-cell">Section 2</th>
                <th scope="col" class="px-6 py-3 hidden md:table-cell">Section 3</th>
                <th scope="col" class="px-6 py-3">Passed</th>
                <th scope="col" class="px-6 py-3 ">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($assessments as $assessment)
                <tr class="odd:bg-white odd:dark:bg-zinc-900 even:bg-gray-50 even:dark:bg-zinc-800 border-b dark:border-zinc-700">
                    <td class="px-6 py-4">
                        {{ $assessment->created_at }}
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell">
                        {{ $assessment->type }}
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell">
                        {{ $assessment->number }}
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell">
                        {{ $assessment->section_one_answers_count - $assessment->incorrect_answers_section_one_count }}/{{ $testAnswerCount[$assessment->number - 1][0]['count'] }}
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell">
                        {{ $assessment->section_two_answers_count - $assessment->incorrect_answers_section_two_count }}/{{ $testAnswerCount[$assessment->number - 1][1]['count'] }}
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell">
                       {{ $assessment->section_three_answers_count - $assessment->incorrect_answers_section_three_count }}/{{ $testAnswerCount[$assessment->number - 1][2]['count'] }}
                    </td>
                    <td class="px-6 py-4">
                        @if (
                            ($assessment->section_one_answers_count - $assessment->incorrect_answers_section_one_count) >= $testAnswerCount[$assessment->number - 1][0]['passMark']
                            && ($assessment->section_two_answers_count - $assessment->incorrect_answers_section_two_count) >= $testAnswerCount[$assessment->number - 1][1]['passMark']
                            && ($assessment->section_three_answers_count - $assessment->incorrect_answers_section_three_count) >= $testAnswerCount[$assessment->number - 1][2]['passMark']
                        )
                            <span class="text-green-500">Passed</span>
                        @else
                            <span class="text-red-500">Failed</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <a href="/assessment/{{ $assessment->type }}/{{ $assessment->number }}/{{ $assessment->id }}/results" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $assessments->links() }}
</div>
