<?php

namespace App\Livewire;

use App\Models\Answer;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\On;
use Livewire\Component;

class Assessment extends Component
{
    public int $section = 0;
    public int $startIndex = 0;
    public int $selectedAnswer = 0;

    public int $testType;
    public int $testNumber;
    public int $assessmentId;

    public array $assessment;

    public string $timeLeft;
    public string $timeEnd;

    public function mount($testType = 0, $testNumber = 0, $assessmentId = 0)
    {
        $this->testType = Route::current()->parameter('testType', $testType);
        $this->testNumber = Route::current()->parameter('testNumber', $testNumber);
        $this->assessmentId = Route::current()->parameter('assessmentId', $assessmentId);
    }

    /**
     * @return View
     */
    public function render(): View
    {
        return view('livewire.assessment');
    }

    /**
     * @return void
     */
    public function handlePreviousQuestion(): void
    {
        $this->setTimeLeft();

        if (
            $this->startIndex === 0
            && $this->section !== 0
        ) {
            $this->section -= 1;
            $this->startIndex = count($this->assessment[$this->section]) - 1;
            $this->selectedAnswer = 0;

            return;
        }

        // Can't go lower than 0
        if ($this->startIndex === 0) {
            return;
        }

        $this->startIndex -= 1;
    }

    /**
     * @param int $answer
     * @return void
     */
    public function handleNextQuestion(int $answer): void
    {
        $this->setTimeLeft();

        // Don't allow answer to be set if it's not in the list of vailable answers
        if (!isset($this->assessment[$this->section][$this->startIndex]['answers'][$answer])) {
            return;
        }

        $isCorrect = ($answer === $this->assessment[$this->section][$this->startIndex]['correct']);

        // Create or update entry on next
        Answer::has('assessment')->updateOrCreate(
            ['assessment_id' => $this->assessmentId, 'section' => $this->section, 'question' => $this->startIndex],
            ['answer' => $answer, 'is_correct' =>  $isCorrect]
        );

        // Don't allow undefined array to load and this would
        // signal the end of the test too
        if (
            !isset($this->assessment[$this->section][$this->startIndex + 1])
            && !isset($this->assessment[$this->section + 1][0])
        ) {
            $this->redirectRoute('loadAssessmentResult', ['testType' => $this->testType, 'testNumber' => $this->testNumber, 'assessmentId' => $this->assessmentId]);
            return;
        }

        $this->selectedAnswer = 0;

        // Go to next section
        if (
            !isset($this->assessment[$this->section][$this->startIndex + 1])
            && isset($this->assessment[$this->section + 1][0])
        ) {
            $this->section += 1;
            $this->startIndex = 0;

            return;
        }

        $this->startIndex += 1;
    }

    /**
     * @return void
     */
    #[On('test-expired')]
    public function handleTestExpired(): void
    {
        $this->redirectRoute('loadAssessmentResult', ['testType' => $this->testType, 'testNumber' => $this->testNumber, 'assessmentId' => $this->assessmentId]);
    }

    /**
     * Function to set $timeEnd and $timeNow
     *
     * @return void
     */
    protected function setTimeLeft(): void
    {
        $timeEnd = Carbon::parse($this->timeEnd);
        $timeNow = Carbon::now()->setTimezone('UTC');

        $this->timeLeft = $timeEnd->diff($timeNow)->format('%Im %Ss');
    }
}
