<?php

namespace Tests\Unit\Livewire;

use App\Livewire\AssessmentIncorrect;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

class AssessmentIncorrectTest extends TestCase
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
     * @param int $index
     * @param int $count
     * @return Testable
     */
    private function getLivewireTest(int $index, int $count): Testable
    {
        return Livewire::test(AssessmentIncorrect::class)
            ->set('index', $index)
            ->set('count', $count)
            ->set('assessment', (array)config('test-one'));
    }

    /**
     * Testing that we don't update the index when it's already zero
     *
     * @return void
     */
    public function test_handle_previous_question_return_early_when_index_is_zero(): void
    {
        $this->getLivewireTest(0, 0)
            ->call('handlePreviousQuestion')
            ->assertSet('index', 0);
    }

    /**
     * Testing that we -1 to the current index
     *
     * @return void
     */
    public function test_handle_previous_question_update_index(): void
    {
        $this->getLivewireTest(2, 0)
            ->call('handlePreviousQuestion')
            ->assertSet('index', 1);
    }

    /**
     * Testing that nothing happens when the index is bigger or the same
     * as the count
     *
     * @return void
     */
    public function test_handle_next_question_index_stays_the_same_when_same_or_bigger_as_count(): void
    {
        $this->getLivewireTest(2, 2)
            ->call('handleNextQuestion')
            ->assertSet('index', 2);
    }

    /**
     * Testing that nothing happens when the index is bigger or the same
     * as the count
     *
     * @return void
     */
    public function test_handle_next_question_update_index_when_smaller_than_count(): void
    {
        $this->getLivewireTest(0, 2)
            ->call('handleNextQuestion')
            ->assertSet('index', 1);
    }
}