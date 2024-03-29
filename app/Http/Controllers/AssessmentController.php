<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;

class AssessmentController extends Controller
{
    /**
     * Creates a new assessment
     *
     * @param int $testType
     * @param int $testNumber
     * @return View|RedirectResponse
     */
    public function createAssessment(int $testType, int $testNumber): View|RedirectResponse
    {
        // Currently we only support testType 2 and testNumber 1
        // others haven't been added yet
        if (
            $testType !== 2
            || $testNumber !== 1
        ) {
            return view('404');
        }

        $assessment = Assessment::create([
            'type' => $testType,
            'number' => $testNumber,
        ]);

        // Caching could be added here if it's a website and not an app

        return redirect()->route('loadAssessment', ['testType' => $testType, 'testNumber' => $testNumber, 'assessmentId' => $assessment->id]);
    }

    /**
     * Loads an existing assessment
     *
     * @param int $testType
     * @param int $testNumber
     * @param int $assessmentId
     * @return View|RedirectResponse
     */
    public function loadAssessment(int $testType, int $testNumber, int $assessmentId): View|RedirectResponse
    {
        // Currently we only support testType 2 and testNumber 1
        // others haven't been added yet
        if (
            $testType !== 2
            || $testNumber !== 1
        ) {
            return view('404');
        }

        // This could be replaced with a cached value if it's a website and
        // not an app
        $assessment = Assessment::with(['latestAnswer'])->firstWhere([
            'id' => $assessmentId,
            'type' => $testType,
            'number' => $testNumber,
        ]);

        if (empty($assessment)) {
            return view('404');
        }

        $timeLeft = Carbon::parse($assessment->created_at)->setTimezone('UTC');
        $timeEnd = $timeLeft->copy()->addHour();

        // No time left, we redirect
        if ($timeEnd < $timeEnd->copy()->now()) {
            return redirect()->route('loadAssessmentResult', ['testType' => $testType, 'testNumber' => $testNumber, 'assessmentId' => $assessment->id]);
        }

        $loadedTest = $this->loadAssessmentConfig($testNumber);

        return view('assessment', [
            'section' => $assessment->latestAnswer->section ?? 0,
            'startIndex' => $assessment->latestAnswer->question ?? 0,
            'selectedAnswer' => $assessment->latestAnswer->answer ?? 0,
            'timeEnd' => $timeEnd->format('Y-m-d H:i:s e'),
            'timeLeft' => $timeLeft->format('Y-m-d H:i:s e'),
            'assessment' => $loadedTest,
        ]);
    }

    /**
     * Loads an existing assessment
     *
     * @param int $testType
     * @param int $testNumber
     * @param int $assessmentId
     * @return View|RedirectResponse
     */
    public function loadAssessmentResult(int $testType, int $testNumber, int $assessmentId): View|RedirectResponse
    {
        $assessment = Assessment::with('answers')->withCount([
            'answers as section_0_correct_answers_count' => function (Builder $query) {
                $query->where('section', '=', 0)
                    ->where('is_correct', '=', true);
            },
            'answers as section_1_correct_answers_count' => function (Builder $query) {
                $query->where('section', '=', 1)
                    ->where('is_correct', '=', true);
            },
            'answers as section_2_correct_answers_count' => function (Builder $query) {
                $query->where('section', '=', 2)
                    ->where('is_correct', '=', true);
            },
        ])->firstWhere([
            'id' => $assessmentId,
            'type' => $testType,
            'number' => $testNumber,
        ]);

        if (empty($assessment)) {
            return view('404');
        }

        $loadedTest = $this->loadAssessmentConfig($testNumber);

        $testPassed = true;
        $assessmentResult = [];
        foreach ($loadedTest as $index => $section) {
            $correctAnswers = $assessment->{'section_' . $index . '_correct_answers_count'} ?? 0;
            $answersForPass = $this->getAnswersNeededForPass($testType, $testNumber, $index);

            $assessmentResult[$index] = [
                'title' => 'Section ' . $index + 1,
                'name' => $this->getSectionName($testType, $testNumber, $index),
                'correct' => $correctAnswers,
                'passMark' => $answersForPass,
                'count' => count($section),
                'hasPassed' => $answersForPass <= $correctAnswers,
            ];

            if (!$assessmentResult[$index]['hasPassed']) {
                $testPassed = false;
            }
        }

        $timeStart = Carbon::parse($assessment->created_at);
        $timeEnd = Carbon::parse($assessment->updated_at);

        $timeSpent = $timeEnd->diff($timeStart)->format('%Im %Ss');

        return view('assessment-results', [
            'timeSpent' => $timeSpent,
            'testPassed' => $testPassed,
            'assessmentResult' => $assessmentResult,
            'incorrectHref' => "/assessment/$testType/$testNumber/$assessmentId/incorrect_answers",
        ]);
    }

    /**
     * View incorrect answers of the assessment
     *
     * @param int $testType
     * @param int $testNumber
     * @param int $assessmentId
     * @return View|RedirectResponse
     */
    public function loadIncorrectAnswers(int $testType, int $testNumber, int $assessmentId): View|RedirectResponse
    {
        // Currently we only support testType 2 and testNumber 1
        // others haven't been added yet
        if (
            $testType !== 2
            || $testNumber !== 1
        ) {
            return view('404');
        }

        // This could be replaced with a cached value if it's a website and
        // not an app
        $assessment = Assessment::with(['incorrectAnswers'])->firstWhere([
            'id' => $assessmentId,
            'type' => $testType,
            'number' => $testNumber,
        ]);

        if (empty($assessment) || empty($assessment->incorrectAnswers)) {
            return view('404');
        }

        $loadedTest = $this->loadAssessmentConfig($testNumber);

        return view('assessment-incorrect_results', [
            'index' => 0,
            'assessment' => $loadedTest,
            'count' => count($assessment->incorrectAnswers) - 1,
            'incorrectAnswers' => $assessment->incorrectAnswers,
        ]);
    }

    /**
     * Returns the assessments view to view all the
     * assessments created
     *
     * @return View
     */
    public function renderAssessments(): View
    {
        return view('assessments');
    }

    /**
     * Function that allows you to load the correct config depending
     * on what $testNumber it's given
     *
     * @param int $testNumber
     * @return array
     */
    private function loadAssessmentConfig(int $testNumber): array
    {
        $loadedTest = [];

        switch ($testNumber) {
            case 1:
                $loadedTest = (array)config('test-one');
                break;
        }

        return $loadedTest;
    }

    /**
     * Function to get the name of the section
     *
     * @param int $testType
     * @param int $testNumber
     * @param int $section
     * @return string
     */
    private function getSectionName(int $testType, int $testNumber, int $section): string
    {
        $names = [
            // Test Type
            2 => [
                // Test Number
                1 => [
                    0 => 'Rules of the road',
                    1 => 'Road signs',
                    2 => 'Controls',
                ]
            ]
        ];

        return $names[$testType][$testNumber][$section] ?? '';
    }

    /**
     * Function to get the name of the section
     *
     * @param int $testType
     * @param int $testNumber
     * @param int $section
     * @return int
     */
    private function getAnswersNeededForPass(int $testType, int $testNumber, int $section): int
    {
        $names = [
            // Test Type
            2 => [
                // Test Number
                1 => [
                    0 => 22,
                    1 => 23,
                    2 => 6,
                ]
            ]
        ];

        return $names[$testType][$testNumber][$section] ?? 0;
    }
}
