<?php

declare(strict_types=1);

namespace Tests;

use Calcagno\Validator\ValidateAttributes;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

final class ValidateAttributesCpfTest extends TestCase
{
  private ValidateAttributes $validator;

  protected function setUp(): void
  {
    $this->validator = new ValidateAttributes();
  }

  /**
   * @testWith ["714.048.700-67"]
   *           ["283.544.940-04"]
   *           ["919.074.140-45"]
   *           ["71404870067"]
   *           ["28354494004"]
   *           ["91907414045"]
   *           [""]
   */
  public function testeValidateCpfValid($value): void
  {
    $this->assertTrue($this->validator->validateCpf($value));
  }

  /**
   * @testWith ["asd"]
   *           ["714.048.700-69"]
   *           ["283.544.940-03"]
   *           ["919.074.140-44"]
   *           ["71404870069"]
   *           ["28354494003"]
   *           ["91907414044"]
   *           ["71404870057"]
   *           ["28354494094"]
   *           ["91907414035"]
   *           ["71404870167"]
   *           ["28354494204"]
   *           ["91907414345"]
   *           ["714.0A8.700-69"]
   *           ["283.5B4.940-03"]
   *           ["919.0C4.140-44"]
   *           ["71404A70069"]
   *           ["28354B94003"]
   *           ["91907C14044"]
   *           ["00000000000"]
   *           ["11111111111"]
   *           ["22222222222"]
   *           ["33333333333"]
   *           ["44444444444"]
   *           ["55555555555"]
   *           ["66666666666"]
   *           ["77777777777"]
   *           ["88888888888"]
   *           ["99999999999"]
   */
  public function testeValidateCpfInvalid($value): void
  {
    $this->assertFalse($this->validator->validateCpf($value));
  }

  /**
   * @testWith [null]
   *           [5]
   *           [8.4]
   *           [true]
   */
  public function testeValidateCpfInvalidType($value): void
  {
    $this->expectException(\TypeError::class);
    $this->validator->validateCpf($value);
  }
}
