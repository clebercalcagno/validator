<?php

declare(strict_types=1);

use Calcagno\Validator\ValidateAttributes;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

final class ValidateAttributesTest extends TestCase
{
  private ValidateAttributes $validator;
  private ReflectionClass $reflection;

  protected function setUp(): void
  {
    $this->validator = new ValidateAttributes();
    $this->reflection = new ReflectionClass($this->validator);
  }

  /**
   * @testWith ["31.950.089/0001-80"]
   *           ["31950089000180"]
   *           ["919.074.140-45"]
   *           ["71404870067"]
   *           [5]
   *           [8.4]
   *           [true]
   *           [["tartaruga", "elefante"]]
   *           [[456, 753]]
   */
  public function testeValidateRequiredValid($value): void
  {
    $this->assertTrue($this->validator->validateRequired($value));
  }

  /**
   * @testWith [null]
   *           [[]]
   *           [""]
   *           [0]
   */
  public function testeValidateRequiredInvalid($value): void
  {
    $this->assertFalse($this->validator->validateRequired($value));
  }

  /**
   * @testWith [[], 0]
   *           [["batata", "cenoura", "beterraba"], 3]
   *           [[456, 89, 741, 146], 4]
   *           ["", 0]
   *           ["123", 3]
   *           ["asdqwe", 6]
   *           ["asd123", 6]
   *           ["asd123*!", 8]
   *           [0, 0]
   *           [9.5, 9.5]
   *           [8.9, 8.9]
   */
  public function testeValidateGetSize($value, $expected): void
  {
    $method = $this->reflection->getMethod('getSize');
    $method->setAccessible(true);

    $actual = $method->invokeArgs($this->validator, [$value]);

    $this->assertSame($expected, $actual);
  }

  /**
   * @testWith [[], 1]
   *           [["carro", "moto", "bicicleta"], 2]
   *           [[46, 89, 742, 16], 3]
   *           ["", 1]
   *           ["789", 2]
   *           ["asdqjh", 5]
   *           ["ajh123", 5]
   *           ["ared123*!", 7]
   *           [9, 1]
   *           [8.51, 9]
   *           [9.84, 8]
   */
  public function testeValidateGetSizeInvalido($value, $expected): void
  {
    $method = $this->reflection->getMethod('getSize');
    $method->setAccessible(true);

    $actual = $method->invokeArgs($this->validator, [$value]);

    $this->assertNotSame($expected, $actual);
  }

  /**
   * @testWith [null]
   */
  public function testeValidateGetSizeInvalidType($value): void
  {
    $this->expectException(\TypeError::class);

    $method = $this->reflection->getMethod('getSize');
    $method->setAccessible(true);
    $method->invokeArgs($this->validator, [$value]);
  }
}
