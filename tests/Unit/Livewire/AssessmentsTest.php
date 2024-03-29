<?php

namespace Tests\Unit\Livewire;

use App\Livewire\Assessments;
use App\Models\Answer;
use App\Models\Assessment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

class AssessmentsTest extends TestCase
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
     * @return Testable
     */
    private function getLivewireTest(): Testable
    {
        return Livewire::test(Assessments::class);
    }

    private function getPassMark(int $assessment, int $section): int
    {
        $passMarks = [
            // assessment number
            0 => [
                // sections
                0 => 22,
                1 => 23,
                2 => 6,
            ]
        ];

        return $passMarks[$assessment][$section] ?: 0;
    }

    /**
     * Testing that the render function returns an empty collection when there's
     * no completed assessments
     *
     * @return void
     */
    public function test_render_returns_nothing_when_there_is_no_assessments(): void
    {
        $test1 = (array)config('test-one');
        $this->getLivewireTest()
            ->assertViewHas('assessments', function ($assessments) {
                $this->assertEquals(1, $assessments->currentPage());
                $this->assertEquals(0, $assessments->total());
                return true;
            })
            ->assertViewHas('testAnswerCount', function ($testAnswerCount) use ($test1) {
                for ($indexRoot = 0; $indexRoot < count($testAnswerCount); $indexRoot++) {
                    for ($index = 0; $index < count($testAnswerCount[$indexRoot]); $index++) {
                        $this->assertEquals(count($test1[$index]), $testAnswerCount[$indexRoot][$index]['count']);
                        $this->assertEquals($this->getPassMark($indexRoot, $index), $testAnswerCount[$indexRoot][$index]['passMark']);
                    }
                }
                return true;          
            });
    }

    /**
     * Testing that we get the correct counts back for a test that has been passed
     * with zero incorrect answers and one with a few incorrect answers
     *
     * @return void
     */
    public function test_render_returns_correct_data_for_each_assessment(): void
    {
        $test1 = (array)config('test-one');

        // Assessment with only correct answers
        Assessment::factory()
            ->lightMotorVehicleTestOne()
            ->has(Answer::factory()
                ->count(22)
                ->sequence(fn ($sequence) => [
                    'section' => 0,
                    'question' => $sequence->index + 1,
                    'is_correct' => true,
                ])
            )
            ->has(Answer::factory()
                ->count(23)
                ->sequence(fn ($sequence) => [
                    'section' => 1,
                    'question' => $sequence->index + 1,
                    'is_correct' => true,
                ])
            )
            ->has(Answer::factory()
                ->count(6)
                ->sequence(fn ($sequence) => [
                    'section' => 2,
                    'question' => $sequence->index + 1,
                    'is_correct' => true,
                ])
            )
            ->expired()
            ->create();

        // Assessment with a few incorrect answers
        Assessment::factory()
            ->lightMotorVehicleTestOne()
            ->has(Answer::factory()
                ->count(21)
                ->sequence(fn ($sequence) => [
                    'section' => 0,
                    'question' => $sequence->index + 1,
                    'is_correct' => true,
                ])
            )
            ->has(Answer::factory()
                ->count(1)
                ->sequence(fn ($sequence) => [
                    'section' => 0,
                    'question' => $sequence->index + 1,
                    'is_correct' => false,
                ])
            )
            ->has(Answer::factory()
                ->count(21)
                ->sequence(fn ($sequence) => [
                    'section' => 1,
                    'question' => $sequence->index + 1,
                    'is_correct' => true,
                ])
            )
            ->has(Answer::factory()
                ->count(2)
                ->sequence(fn ($sequence) => [
                    'section' => 1,
                    'question' => $sequence->index + 1,
                    'is_correct' => false,
                ])
            )
            ->has(Answer::factory()
                ->count(3)
                ->sequence(fn ($sequence) => [
                    'section' => 2,
                    'question' => $sequence->index + 1,
                    'is_correct' => true,
                ])
            )
            ->has(Answer::factory()
                ->count(3)
                ->sequence(fn ($sequence) => [
                    'section' => 2,
                    'question' => $sequence->index + 1,
                    'is_correct' => false,
                ])
            )
            ->expired()
            ->create();

        // Testing we get the correct responses back
        $this->getLivewireTest()
            ->assertViewHas('assessments', function ($assessments) {
                $this->assertEquals(1, $assessments->currentPage());
                $this->assertEquals(2, $assessments->total());

                $this->assertEquals(22, $assessments->items()[0]->section_one_answers_count);
                $this->assertEquals(23, $assessments->items()[0]->section_two_answers_count);
                $this->assertEquals(6, $assessments->items()[0]->section_three_answers_count);

                $this->assertEquals(0, $assessments->items()[0]->incorrect_answers_section_one_count);
                $this->assertEquals(0, $assessments->items()[0]->incorrect_answers_section_two_count);
                $this->assertEquals(0, $assessments->items()[0]->incorrect_answers_section_three_count);

                $this->assertEquals(22, $assessments->items()[1]->section_one_answers_count);
                $this->assertEquals(23, $assessments->items()[1]->section_two_answers_count);
                $this->assertEquals(6, $assessments->items()[1]->section_three_answers_count);

                $this->assertEquals(1, $assessments->items()[1]->incorrect_answers_section_one_count);
                $this->assertEquals(2, $assessments->items()[1]->incorrect_answers_section_two_count);
                $this->assertEquals(3, $assessments->items()[1]->incorrect_answers_section_three_count);
                return true;
            })
            ->assertViewHas('testAnswerCount', function ($testAnswerCount) use ($test1) {
                for ($indexRoot = 0; $indexRoot < count($testAnswerCount); $indexRoot++) {
                    for ($index = 0; $index < count($testAnswerCount[$indexRoot]); $index++) {
                        $this->assertEquals(count($test1[$index]), $testAnswerCount[$indexRoot][$index]['count']);
                        $this->assertEquals($this->getPassMark($indexRoot, $index), $testAnswerCount[$indexRoot][$index]['passMark']);
                    }
                }
                return true;          
            });
    }
}