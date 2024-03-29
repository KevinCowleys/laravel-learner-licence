<?php

namespace Tests\Unit;

use Tests\TestCase;

class AssessmentConfigTest extends TestCase
{
    /**
     * Testing to make sure tests are in the correct format
     * and have all the requirements like images
     *
     * @return void
     */
    public function test_assessment_setup_is_correct(): void
    {
        foreach ($this->tests as $testName) {
            $loadedTest = config($testName);

            $this->assessmentSectionsExists($testName, $loadedTest);

            for ($section = 0; $section <= $this->sections[$testName] - 1; $section++) {
                foreach ($loadedTest[$section] as $qIndex => $question) {
                    // Check that we have the correct answer set
                    $this->assertTrue(isset($question['correct']));
                    // Check that we have the answers array set
                    $this->assertTrue(isset($question['answers']));
                    // Checks that the correct answer exists in the answer array
                    $this->assertTrue(isset($question['answers'][$question['correct']]));

                    // Checks that the image exists in the file system
                    if (
                        isset($question['image'])
                        && !file_exists(public_path() . '/images/' . $question['image'])
                    ) {
                        $this->fail("Image does not exist. Test - $testName, Section - $section, Question - $qIndex, Image - " . $question['image']);
                    }
                }
            }
        }
    }
}
