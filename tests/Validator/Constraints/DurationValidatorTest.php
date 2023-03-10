<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Validator\Constraints;

use App\Validator\Constraints\Duration;
use App\Validator\Constraints\DurationValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @covers \App\Validator\Constraints\Duration
 * @covers \App\Validator\Constraints\DurationValidator
 */
class DurationValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator()
    {
        return new DurationValidator();
    }

    public function getValidData()
    {
        return [
            ['2h'],
            ['38m'],
            ['99s'],
            ['2h38m'],
            ['2h38s'],
            ['2m38s'],
            ['2h38m17s'],
            ['1h96m137s'],
            [''],
            ['0'],
            ['1.2'],
            ['2,3'],
            [null],
            [0],
            [11257200],
            ['13:27'],
            ['13:27:54'],
            ['12:87:54'],
            ['3127:00:00'],
            ['3127:00'],
            [48474],
        ];
    }

    public function testConstraintIsInvalid()
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate('foo', new NotBlank());
    }

    /**
     * @dataProvider getValidData
     * @param string $input
     */
    public function testConstraintWithValidData($input)
    {
        $constraint = new Duration();
        $this->validator->validate($input, $constraint);
        if ($input !== null) {
            $input = strtoupper($input);
        }
        $this->validator->validate($input, $constraint);
        $this->assertNoViolation();
    }

    public function getInvalidData()
    {
        return [
            ['13-13'],
            ['2m3m'],
            ['2s3s'],
            ['2h3h'],
            ['2m3h'],
            ['2s3h'],
            ['2s3m'],
            ['3127::00'],
            ['3127:00:'],
            [':3127:00'],
            ['::3127'],
            ['foo'],
        ];
    }

    /**
     * @dataProvider getInvalidData
     * @param mixed $input
     */
    public function testValidationError($input)
    {
        $constraint = new Duration([
            'message' => 'myMessage',
        ]);

        $this->validator->validate($input, $constraint);

        $this->buildViolation('myMessage')
            ->setParameter('{{ value }}', '"' . $input . '"')
            ->setCode(Regex::REGEX_FAILED_ERROR)
            ->assertRaised();
    }

    /**
     * @dataProvider getInvalidData
     * @param mixed $input
     */
    public function testValidationErrorUpperCase($input)
    {
        $input = strtoupper($input);
        $constraint = new Duration([
            'message' => 'myMessage',
        ]);

        $this->validator->validate($input, $constraint);

        $this->buildViolation('myMessage')
            ->setParameter('{{ value }}', '"' . $input . '"')
            ->setCode(Regex::REGEX_FAILED_ERROR)
            ->assertRaised();
    }
}
