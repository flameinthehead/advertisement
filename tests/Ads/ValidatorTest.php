<?php

use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    public function testError1()
    {
        $request = [
            'text' => 'Simple advertisement',
        ];
        $this->validator = new App\Validators\AdsValidator($request);

        $this->assertFalse($this->validator->validate());

        $errors = $this->validator->getErrors();
        $this->assertNotEmpty($errors);
        $this->assertNotEmpty($this->validator->getFirstErrorMessage());
        $this->assertArrayHasKey('price', $errors);
        $this->assertArrayHasKey('limit', $errors);
        $this->assertArrayHasKey('banner', $errors);

        $requiredErrorsCount = 0;
        $numericErrorsCount = 0;
        $urlErrorsCount = 0;

        foreach($errors as $error){
            if(in_array('required', $error)){
                ++$requiredErrorsCount;
            }

            if(in_array('numeric', $error)){
                ++$numericErrorsCount;
            }

            if(in_array('url', $error)){
                ++$urlErrorsCount;
            }
        }

        $this->assertEquals(3, $requiredErrorsCount);
        $this->assertEquals(2, $numericErrorsCount);
        $this->assertEquals(1, $urlErrorsCount);
    }

    public function testError2()
    {
        $request = [
            'text' => 'Simple advertisement',
            'price' => 'asd',
            'limit' => 'zzzz',
            'banner' => 'some text'
        ];
        $this->validator = new App\Validators\AdsValidator($request);
        $this->assertFalse($this->validator->validate());
        $errors = $this->validator->getErrors();
        $this->assertArrayHasKey('price', $errors);
        $this->assertArrayHasKey('limit', $errors);
        $this->assertArrayHasKey('banner', $errors);

        $requiredErrorsCount = 0;
        $numericErrorsCount = 0;
        $urlErrorsCount = 0;

        foreach($errors as $error){
            if(in_array('required', $error)){
                ++$requiredErrorsCount;
            }

            if(in_array('numeric', $error)){
                ++$numericErrorsCount;
            }

            if(in_array('url', $error)){
                ++$urlErrorsCount;
            }
        }

        $this->assertEquals(0, $requiredErrorsCount);
        $this->assertEquals(2, $numericErrorsCount);
        $this->assertEquals(1, $urlErrorsCount);
    }

    public function testSuccess()
    {
        $request = [
            'text' => 'Simple advertisement',
            'price' => 777,
            'limit' => 10,
            'banner' => 'http://example.com/path-to-image.jpg'
        ];
        $this->validator = new App\Validators\AdsValidator($request);
        $this->assertTrue($this->validator->validate());
        $this->assertEmpty($this->validator->getErrors());
        $this->assertEmpty($this->validator->getFirstErrorMessage());
    }
}
