<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\AssessmentController;
use App\Models\Answer;
use App\Models\Assessment;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class AssessmentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected AssessmentController&\Mockery\MockInterface $assessmentController;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->assessmentController = Mockery::mock(AssessmentController::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    /**
     * @param int $correctOne 22/30 needed for pass
     * @param int $correctTwo 23/30 needed for pass
     * @param int $correctThree 6/8 needed for pass
     * @return array
     */
    private function createAssessmentResult($correctOne = 0, $correctTwo = 0, $correctThree = 0): array
    {
        return [
            0 => [
                'title' => 'Section 1',
                'name' => 'Rules of the road',
                'correct' => $correctOne,
                'passMark' => 22,
                'count' => 30,
                'hasPassed' => $correctOne >= 22,
            ],
            1 => [
                'title' => 'Section 2',
                'name' => 'Road signs',
                'correct' => $correctTwo,
                'passMark' => 23,
                'count' => 30,
                'hasPassed' => $correctTwo >= 23,
            ],
            2 => [
                'title' => 'Section 3',
                'name' => 'Controls',
                'correct' => $correctThree,
                'passMark' => 6,
                'count' => 8,
                'hasPassed' => $correctThree >= 6,
            ],
        ];
    }

    /**
     * Testing that we get the 404 view when test type and test number
     * don't exist
     *
     * @return void
     */
    public function test_create_assessment_404_view(): void
    {
        $response = $this->assessmentController->createAssessment(3, 3);

        $this->assertEquals($response, view('404'));
    }

    /**
     * Testing that we get redirected to the correct route with a proper
     * assessmentId
     *
     * @return void
     */
    public function test_create_assessment_redirect_with_data(): void
    {
        $response = $this->assessmentController->createAssessment(2, 1);

        $this->assertEquals($response, redirect()->route('loadAssessment', ['testType' => 2, 'testNumber' => 1, 'assessmentId' => 1]));
    }

    /**
     * Testing that we get the 404 view when test type and test number
     * don't exist
     *
     * @return void
     */
    public function test_load_assessment_404_view(): void
    {
        $response = $this->assessmentController->loadAssessment(3, 3, 1);

        $this->assertEquals($response, view('404'));
    }

    /**
     * Testing that we get the 404 view when the test doesn't exist
     *
     * @return void
     */
    public function test_load_assessment_404_view_when_assessment_does_not_exist_in_db(): void
    {
        $response = $this->assessmentController->loadAssessment(2, 1, 1);

        $this->assertEquals($response, view('404'));
    }

    /**
     * Testing that we redirect to assessment results when the test
     * has expired
     *
     * @return void
     */
    public function test_load_assessment_redirects_when_time_is_up(): void
    {
        $assessment = Assessment::factory()
            ->lightMotorVehicleTestOne()
            ->expired()
            ->create();

        $response = $this->assessmentController->loadAssessment(2, 1, $assessment->id);

        $this->assertEquals($response, redirect()->route('loadAssessmentResult', [
            'testType' => 2,
            'testNumber' => 1,
            'assessmentId' => $assessment->id
        ]));
    }

    /**
     * Testing that we get a proper assessment view back like we expect
     *
     * @return void
     */
    public function test_load_assessment_returns_assessment_view(): void
    {
        $assessment = Assessment::factory()
            ->lightMotorVehicleTestOne()
            ->create();

        $response = $this->assessmentController->loadAssessment(2, 1, $assessment->id);

        $timeLeft = Carbon::parse($assessment->created_at)->setTimezone('UTC');
        $timeEnd = $timeLeft->copy()->addHour();

        $this->assertEquals($response, view('assessment', [
            'section' => $assessment->latestAnswer->section ?? 0,
            'startIndex' => $assessment->latestAnswer->question ?? 0,
            'selectedAnswer' => $assessment->latestAnswer->answer ?? 0,
            'timeEnd' => $timeEnd->format('Y-m-d H:i:s e'),
            'timeLeft' => $timeLeft->format('Y-m-d H:i:s e'),
            'assessment' => (array)config('test-one'),
        ]));
    }

    /**
     * Test that we get a 404 view when the assessment does not exist
     *
     * @return void
     */
    public function test_load_assessment_result_returns_404_when_does_not_exist(): void
    {
        $response = $this->assessmentController->loadAssessmentResult(2, 1, 1);

        $this->assertEquals($response, view('404'));
    }

    /**
     * Test that we get a false testPassed when the test has missing answers or
     * not enough correct ones
     *
     * @return void
     */
    public function test_load_assessment_result_has_not_passed(): void
    {
        $assessment = Assessment::factory()
            ->lightMotorVehicleTestOne()
            ->expired()
            ->create();

        $timeStart = Carbon::parse($assessment->created_at);
        $timeEnd = Carbon::parse($assessment->updated_at);
    
        $timeSpent = $timeEnd->diff($timeStart)->format('%Im %Ss');

        $testType = 2;
        $testNumber = 1;

        $response = $this->assessmentController->loadAssessmentResult(2, 1, $assessment->id);

        $assessmentResult = $this->createAssessmentResult();

        $this->assertEquals($response, view('assessment-results', [
            'timeSpent' => $timeSpent,
            'testPassed' => false,
            'assessmentResult' => $assessmentResult,
            'incorrectHref' => "/assessment/$testType/$testNumber/$assessment->id/incorrect_answers",
        ]));
    }

    /**
     * Test that we get a false testPassed when the test has missing answers or
     * not enough correct ones in multiple sections
     *
     * @return void
     */
    public function test_load_assessment_fails_without_all_sections_passing(): void
    {
        $assessment = Assessment::factory()
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
                ->count(6)
                ->sequence(fn ($sequence) => [
                    'section' => 2,
                    'question' => $sequence->index + 1,
                    'is_correct' => true,
                ])
            )
            ->expired()
            ->create();

        $timeStart = Carbon::parse($assessment->created_at);
        $timeEnd = Carbon::parse($assessment->updated_at);
    
        $timeSpent = $timeEnd->diff($timeStart)->format('%Im %Ss');

        $testType = 2;
        $testNumber = 1;

        $response = $this->assessmentController->loadAssessmentResult(2, 1, $assessment->id);

        $assessmentResult = $this->createAssessmentResult(22, 0, 6);

        $this->assertEquals($response, view('assessment-results', [
            'timeSpent' => $timeSpent,
            'testPassed' => false,
            'assessmentResult' => $assessmentResult,
            'incorrectHref' => "/assessment/$testType/$testNumber/$assessment->id/incorrect_answers",
        ]));
    }

    /**
     * Test that we get a true testPassed when the test has all sections
     * passing
     *
     * @return void
     */
    public function test_load_assessment_passed(): void
    {
        $assessment = Assessment::factory()
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

        $timeStart = Carbon::parse($assessment->created_at);
        $timeEnd = Carbon::parse($assessment->updated_at);
    
        $timeSpent = $timeEnd->diff($timeStart)->format('%Im %Ss');

        $testType = 2;
        $testNumber = 1;

        $response = $this->assessmentController->loadAssessmentResult(2, 1, $assessment->id);

        $assessmentResult = $this->createAssessmentResult(22, 23, 6);

        $this->assertEquals($response, view('assessment-results', [
            'timeSpent' => $timeSpent,
            'testPassed' => true,
            'assessmentResult' => $assessmentResult,
            'incorrectHref' => "/assessment/$testType/$testNumber/$assessment->id/incorrect_answers",
        ]));
    }

    /**
     * Test that we get a 404 view when the assessment isn't a valid one
     *
     * @return void
     */
    public function test_load_incorrect_answers_404_view_when_assessment_does_not_exist(): void
    {
        $response = $this->assessmentController->loadIncorrectAnswers(3, 3, 1);

        $this->assertEquals($response, view('404'));
    }

    /**
     * Test that we get a 404 view when the assessment doesn't exist in the DB
     *
     * @return void
     */
    public function test_load_incorrect_answers_404_view_when_assessment_does_not_exist_in_db_or_no_incorrect_answers(): void
    {
        $response = $this->assessmentController->loadIncorrectAnswers(2, 1, 1);

        $this->assertEquals($response, view('404'));
    }

    /**
     * Test that we can load the incorrect answers as expected
     *
     * @return void
     */
    public function test_load_incorrect_answers_returns_incorrect_results_view(): void
    {
        $assessment = Assessment::factory()
        ->lightMotorVehicleTestOne()
        ->has(Answer::factory()
            ->count(22)
            ->sequence(fn ($sequence) => [
                'section' => 0,
                'question' => $sequence->index + 1,
                'is_correct' => false,
            ])
        )
        ->has(Answer::factory()
            ->count(6)
            ->sequence(fn ($sequence) => [
                'section' => 2,
                'question' => $sequence->index + 1,
                'is_correct' => false,
            ])
        )
        ->expired()
        ->create();

        $loadedTest = (array)config('test-one');

        $response = $this->assessmentController->loadIncorrectAnswers(2, 1, $assessment->id);

        $this->assertEquals($response, view('assessment-incorrect_results', [
            'index' => 0,
            'assessment' => $loadedTest,
            'count' => count($assessment->incorrectAnswers) - 1,
            'incorrectAnswers' => $assessment->incorrectAnswers,
        ]));
    }

    /**
     * Testing that we get the 404 view when the test doesn't exist
     *
     * @return void
     */
    public function test_render_assessments(): void
    {
        $response = $this->assessmentController->renderAssessments();

        $this->assertEquals($response, view('assessments'));
    }
}
