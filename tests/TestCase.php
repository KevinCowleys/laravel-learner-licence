<?php

namespace Tests;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * The current tests we use
     */
    protected array $tests = [
        'test-one',
    ];

    /**
     * Amount of sections each test has
     */
    protected array $sections = [
        'test-one' => 3,
    ];

    /**
     * @param string $fileName
     * @param array $loadedTest
     * @return void
     */
    public function assessmentSectionsExists(string $testName, array $loadedTest): void
    {
        switch ($this->sections[$testName]) {
            case (1):
                $this->assertTrue(isset($loadedTest[0]));
                break;
            case (2):
                $this->assertTrue(isset($loadedTest[0]));
                $this->assertTrue(isset($loadedTest[1]));
                break;
            case (3):
                $this->assertTrue(isset($loadedTest[0]));
                $this->assertTrue(isset($loadedTest[1]));
                $this->assertTrue(isset($loadedTest[2]));
                break;
        }
    }
}
