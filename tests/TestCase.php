<?php

namespace Garissman\Printify\Tests;

use Exception;
use Garissman\Printify\PrintifyApiClient;
use Garissman\Printify\Tests\Credentials;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $api;

    protected function setUp()
    {
        parent::setUp();

        if (!class_exists(Credentials::class)) {
            throw new Exception('Printify test credentials are not set. Copy "tests/Credentials.php.dist" to "tests/Credentials.php and enter your token');
        }

        $this->api = new PrintifyApiClient(Credentials::$token);
    }

    /**
     * Assert the array has a given structure.
     *
     * @param  array  $structure
     * @param  array  $arrayData
     * @return $this
     */
    public function assertArrayStructure(array $structure, array $arrayData)
    {
        foreach ($structure as $key => $value) {
            if (is_array($value) && $key === '*') {
                $this->assertInternalType('array', $arrayData);

                foreach ($arrayData as $arrayDataItem) {
                    $this->assertArrayStructure($structure['*'], $arrayDataItem);
                }
            } elseif (is_array($value)) {
                $this->assertArrayHasKey($key, $arrayData);

                $this->assertArrayStructure($structure[$key], $arrayData[$key]);
            } else {
                $this->assertArrayHasKey($value, $arrayData);
            }
        }

        return $this;
    }

}
