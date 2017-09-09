<?php

namespace Tests\Packages\Ngtfkx\LaradeckAddressGenerator;

use Illuminate\Support\Collection;
use Ngtfkx\Laradeck\AddressGenerator\Generator;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomDataTest extends TestCase
{
    /**
     * @var Generator $generator
     */
    protected $generator;

    protected function setUp()
    {
        parent::setUp();

        $this->generator = new Generator();

        $cityId = 111;

        $cityName = ['Нефтебаза', 'Netftebaza'];

        $file = 'app/111.php';

        $this->generator->loadCustomData($cityId, $cityName, storage_path($file));
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->generator = null;
    }

    public function testAddCitiesAsOne()
    {
        $this->assertEquals(new Collection('Нефтебаза'), $this->generator->getCityNames());
    }

    public function testGetRandomAddress()
    {
        $address = $this->generator->getRandomAddress();

        $this->assertSame('Нефтебаза', $address->getLocality());
    }
}
