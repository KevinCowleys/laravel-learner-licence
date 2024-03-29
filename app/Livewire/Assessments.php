<?php

namespace App\Livewire;

use App\Models\Assessment;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Assessments extends Component
{
    use WithPagination;

    public function mount()
    {
    }

    /**
     * @return View
     */
    public function render(): View
    {
        $assessments = Assessment::withCount([
            'sectionOneAnswers',
            'sectionTwoAnswers',
            'sectionThreeAnswers',
            'incorrectAnswersSectionOne',
            'incorrectAnswersSectionTwo',
            'incorrectAnswersSectionThree',
        ])->with('answers')->paginate(15);

        $testAnswerCount = [];
        $test1 = (array)config('test-one') ?? [];

        if (isset($test1[0]) && isset($test1[1]) && isset($test1[2])) {
            $testAnswerCount[0][0]['count'] = count($test1[0]);
            $testAnswerCount[0][0]['passMark'] = 22;
            $testAnswerCount[0][1]['count'] = count($test1[1]);
            $testAnswerCount[0][1]['passMark'] = 23;
            $testAnswerCount[0][2]['count'] = count($test1[2]);
            $testAnswerCount[0][2]['passMark'] = 6;
        }

        return view('livewire.assessments', [
            'assessments' => $assessments,
            'testAnswerCount' => $testAnswerCount,
        ]);
    }
}
