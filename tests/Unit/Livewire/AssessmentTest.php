<?php

namespace Tests\Unit\Livewire;

use App\Livewire\Assessment;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

class AssessmentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @param int $section
     * @param int $startIndex
     * @param int $selectedIndex
     * @param int $testType
     * @param int $testNumber
     * @param int $assessmentId
     * @param array $assessment
     * @return Testable
     */
    private function getLivewireTest(int $section, int $startIndex, int $selectedIndex, int $testType, int $testNumber, int $assessmentId, string $timeEnd = ''): Testable
    {
        return Livewire::test(Assessment::class, [
                'section' => $section,
                'startIndex' => $startIndex,
                'selectedAnswer' => $selectedIndex,
            ])
            ->set('testType', $testType)
            ->set('testNumber', $testNumber)
            ->set('assessmentId', $assessmentId)
            ->set('timeEnd', $timeEnd === '' ? Carbon::now() : $timeEnd)
            ->set('assessment', (array)config('test-one'));
    }

    /**
     * Testing that nothing changes when section and startIndex is 0
     *
     * @return void
     */
    public function test_handle_previous_question_does_not_update_when_at_start(): void
    {
        $this->getLivewireTest(0, 0, 0, 0, 0, 0)
            ->call('handlePreviousQuestion')
            ->assertSet('section', 0)
            ->assertSet('startIndex', 0)
            ->assertSet('selectedAnswer', 0);
    }

    /**
     * Testing that section gets changed to 0 and startIndex gets changed to 29
     *
     * @return void
     */
    public function test_handle_previous_question_updates_startIndex_when_going_back_a_section(): void
    {
        $this->getLivewireTest(1, 0, 0, 0, 0, 0)
            ->call('handlePreviousQuestion')
            ->assertSet('section', 0)
            ->assertSet('startIndex', 29)
            ->assertSet('selectedAnswer', 0);
    }

    /**
     * Testing that only the startIndex gets changed when startIndex isn't zero
     *
     * @return void
     */
    public function test_handle_previous_question_only_updates_startIndex_when_not_zero(): void
    {
        $this->getLivewireTest(0, 20, 0, 0, 0, 0)
            ->call('handlePreviousQuestion')
            ->assertSet('section', 0)
            ->assertSet('startIndex', 19)
            ->assertSet('selectedAnswer', 0);
    }

    /**
     * Testing that we exist early when the answer doesn't exist as a possible answer
     *
     * @return void
     */
    public function test_handle_next_question_return_early_when_answer_does_not_exist(): void
    {
        $this->getLivewireTest(0, 0, 0, 0, 0, 0)
            ->call('handleNextQuestion', 5)
            ->assertSet('section', 0)
            ->assertSet('startIndex', 0)
            ->assertSet('selectedAnswer', 0);
    }

    /**
     * Testing that we exit early when there's no new sections or questions available
     *
     * @return void
     */
    public function test_handle_next_question_return_early_when_end_of_test(): void
    {
        $this->getLivewireTest(2, 7, 0, 0, 0, 0)
            ->call('handleNextQuestion', 1)
            ->assertRedirect('assessment/0/0/0/results')
            ->assertSet('section', 2)
            ->assertSet('startIndex', 7)
            ->assertSet('selectedAnswer', 0);
    }

    /**
     * Testing that we go to a new section when the last question in a section gets reached
     *
     * @return void
     */
    public function test_handle_next_question_go_to_next_section(): void
    {
        $this->getLivewireTest(1, 29, 0, 0, 0, 0)
            ->call('handleNextQuestion', 1)
            ->assertSet('section', 2)
            ->assertSet('startIndex', 0)
            ->assertSet('selectedAnswer', 0);
    }

    /**
     * Testing we go to just the next question if there's more available in current section
     *
     * @return void
     */
    public function test_handle_next_question_go_to_next_question(): void
    {
        $this->getLivewireTest(0, 0, 0, 0, 0, 0)
            ->call('handleNextQuestion', 1)
            ->assertSet('section', 0)
            ->assertSet('startIndex', 1)
            ->assertSet('selectedAnswer', 0);
    }

    /**
     * Testing that handleTestExpired redirects properly
     *
     * @return void
     */
    public function test_handle_test_expired(): void
    {
        $this->getLivewireTest(0, 0, 0, 0, 0, 0)
            ->call('handleTestExpired')
            ->assertRedirect('assessment/0/0/0/results');
    }
}
