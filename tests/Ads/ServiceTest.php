<?php

use App\Entity\Ads;
use App\Services\AdsService;
use App\Storage\ArrayStorage;
use PHPUnit\Framework\TestCase;

class ServiceTest extends TestCase
{
    private $service;

    public function setUp(): void
    {
        $storage = new ArrayStorage();
        $entity = new Ads($storage);
        $this->service = new AdsService($entity);
    }

    public function testAdd()
    {
        $ads = [
            'text' => 'Ads #1',
            'price' => 1000,
            'limit' => 5,
            'banner' => 'http://example.com/path-to-image.png'
        ];

        $expected = [
            'id' => 1,
            'text' => 'Ads #1',
            'banner' => 'http://example.com/path-to-image.png',
        ];
        $this->assertEqualsCanonicalizing($expected, $this->service->add($ads));

        $this->expectExceptionMessage('Same advertisement is already exists');
        $this->service->add($ads);
    }

    public function testRelevant1()
    {
        $this->expectExceptionMessage('There is no available ads');
        $this->service->relevant();
    }

    public function testRelevant2()
    {
        $ads = [
            'text' => 'Ads #1',
            'price' => 1000,
            'limit' => 0,
            'banner' => 'http://example.com/path-to-image.png'
        ];
        $this->service->add($ads);

        $this->expectExceptionMessage('There is no available ads');
        $this->service->relevant();
    }

    public function testRelevant3()
    {
        $ads1 = [
            'text' => 'Ads #1',
            'price' => 1000,
            'limit' => 3,
            'banner' => 'http://example.com/path-to-image.png'
        ];
        $this->service->add($ads1);

        $ads2 = [
            'text' => 'Ads #2',
            'price' => 500,
            'limit' => 2,
            'banner' => 'http://example.com/path-to-image.png'
        ];
        $this->service->add($ads2);

        $ads3 = [
            'text' => 'Ads #3',
            'price' => 7000,
            'limit' => 4,
            'banner' => 'http://example.com/path-to-image.png'
        ];
        $this->service->add($ads3);


        $expect = [
            'id' => 3,
            'text' => 'Ads #3',
            'banner' => 'http://example.com/path-to-image.png'
        ];
        $this->assertEqualsCanonicalizing($expect, $this->service->relevant());
        $ads = $this->service->get(3);

        $expect = [
            'text' => 'Ads #3',
            'price' => 7000,
            'limit' => 3,
            'banner' => 'http://example.com/path-to-image.png',
            'id' => 3,
        ];

        $this->assertEqualsCanonicalizing($expect, $ads);


        $this->service->relevant();
        $this->service->relevant();
        $this->service->relevant();
        $ads = $this->service->get(3);
        $expect = [
            'text' => 'Ads #3',
            'price' => 7000,
            'limit' => 0,
            'banner' => 'http://example.com/path-to-image.png',
            'id' => 3,
        ];
        $this->assertEqualsCanonicalizing($expect, $ads);

        $result = $this->service->relevant();
        $expected = [
            'id' => 1,
            'text' => 'Ads #1',
            'banner' => 'http://example.com/path-to-image.png',

        ];
        $this->assertEqualsCanonicalizing($expected, $result);
    }


}
