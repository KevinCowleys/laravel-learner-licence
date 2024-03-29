<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class AssessmentIncorrect extends Component
{
    public int $index;
    public int $count;

    public array $assessment;
    public Collection $incorrectAnswers;

    public function mount()
    {
    }

    /**
     * @return View
     */
    public function render(): View
    {
        return view('livewire.assessment_incorrect');
    }

    /**
     * @return void
     */
    public function handlePreviousQuestion(): void
    {
        if ($this->index === 0) {
            return;
        }

        $this->index -= 1;
    }

    /**
     * @param int $answer
     * @return void
     */
    public function handleNextQuestion(): void
    {
        if ($this->index < $this->count) {
            $this->index += 1;
            return;
        }
    }
}
