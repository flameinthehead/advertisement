<?php

use App\Entity\Ads;
use App\Storage\ArrayStorage;
use PHPUnit\Framework\TestCase;

class EntityTest extends TestCase
{
    private $entity;

    public function setUp(): void
    {
        $this->entity = new Ads(new ArrayStorage());
    }

    public function testCheckExists()
    {
        $ads = [
            'text' => 'Ads #1',
            'price' => 1000,
            'limit' => 5,
            'banner' => 'http://example.com/path-to-image.png'
        ];
        $this->assertFalse($this->entity->checkExists($ads));
        $this->entity->add($ads);
        $this->assertTrue($this->entity->checkExists($ads));
    }

    public function testAdd()
    {
        $ads = [
            'text' => 'Ads #1',
            'price' => 1000,
            'limit' => 5,
            'banner' => 'http://example.com/path-to-image.png'
        ];
        $expected = $ads;
        $expected['id'] = 1;

        $this->assertEqualsCanonicalizing($expected, $this->entity->add($ads));

        $ads = [
            'text' => 'Ads #2',
            'price' => 333,
            'limit' => 8,
            'banner' => 'http://example.com/path-to-image2.png'
        ];
        $expected = $ads;
        $expected['id'] = 2;

        $this->assertEqualsCanonicalizing($expected, $this->entity->add($ads));
    }

    public function testGet()
    {
        $this->expectExceptionMessage('Ads with id = 777 not found');
        $this->entity->get(777);

        $ads = [
            'text' => 'Ads #1',
            'price' => 1000,
            'limit' => 5,
            'banner' => 'http://example.com/path-to-image.png'
        ];
        $this->entity->add($ads);
        $expected = $ads;
        $expected['id'] = 1;

        $this->assertEqualsCanonicalizing($expected, $this->get(1));
    }

    public function testUpdate()
    {
        $ads = [
            'text' => 'Ads #1',
            'price' => 1000,
            'limit' => 5,
            'banner' => 'http://example.com/path-to-image.png'
        ];

        $added = $this->entity->add($ads);
        $this->assertEquals(1, $added['id']);

        $update = [
            'limit' => 7,
            'price' => 123,
        ];
        $this->entity->update(1, $update);

        $updatedAds = $this->entity->get(1);
        $expected = [
            'text' => 'Ads #1',
            'price' => 123,
            'limit' => 7,
            'banner' => 'http://example.com/path-to-image.png',
            'id' => 1
        ];
        $this->assertEqualsCanonicalizing($expected, $updatedAds);
    }

    public function testMostExpensive()
    {
        $ads1 = [
            'text' => 'Ads #1',
            'price' => 555,
            'limit' => 3,
            'banner' => 'http://example.com/path-to-image1.png'
        ];

        $this->entity->add($ads1);

        $ads2 = [
            'text' => 'Ads #2',
            'price' => 7000,
            'limit' => 10,
            'banner' => 'http://example.com/path-to-image2.png'
        ];

        $this->entity->add($ads2);

        $ads3 = [
            'text' => 'Ads #3',
            'price' => 123,
            'limit' => 4,
            'banner' => 'http://example.com/path-to-image3.png'
        ];

        $this->entity->add($ads3);

        $expensive = $this->entity->getMostExpensive();
        $this->assertEquals('Ads #2', $expensive['text']);
    }
}
