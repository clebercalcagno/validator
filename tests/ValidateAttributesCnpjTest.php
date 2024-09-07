<?php

declare(strict_types=1);

namespace Tests;

use Calcagno\Validator\ValidateAttributes;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

final class ValidateAttributesCnpjTest extends TestCase
{
  private ValidateAttributes $validator;

  protected function setUp(): void
  {
    $this->validator = new ValidateAttributes();
  }

  /**
   * @testWith ["31.950.089/0001-80"]
   *           ["36.559.666/0001-21"]
   *           ["45.459.538/0001-05"]
   *           ["31950089000180"]
   *           ["36559666000121"]
   *           ["45459538000105"]
   *           [""]
   */
  public function testeValidateCnpjValid($value): void
  {
    $this->assertTrue($this->validator->validateCnpj($value));
  }

  /**
   * @testWith ["asd"]
   *           ["31.950.089/0001-81"]
   *           ["36.559.666/0001-22"]
   *           ["45.459.538/0001-06"]
   *           ["31950089000181"]
   *           ["36559666000122"]
   *           ["45459538000106"]
   *           ["31.950.089/0001-90"]
   *           ["36.559.666/0001-31"]
   *           ["45.459.538/0001-15"]
   *           ["31950089000190"]
   *           ["36559666000131"]
   *           ["45459538000115"]
   *           ["31.960.089/0001-80"]
   *           ["36.569.666/0001-21"]
   *           ["45.469.538/0001-05"]
   *           ["31960089000180"]
   *           ["36569666000121"]
   *           ["45469538000105"]
   *           ["3A.950.089/0001-80"]
   *           ["3B.559.666/0001-21"]
   *           ["4C.459.538/0001-05"]
   *           ["3D950089000180"]
   *           ["3E559666000121"]
   *           ["4F459538000105"]
   *           ["00000000000000"]
   *           ["11111111111111"]
   *           ["22222222222222"]
   *           ["33333333333333"]
   *           ["44444444444444"]
   *           ["55555555555555"]
   *           ["66666666666666"]
   *           ["77777777777777"]
   *           ["88888888888888"]
   *           ["99999999999999"]
   */
  public function testeValidateCnpjInvalid($value): void
  {
    $this->assertFalse($this->validator->validateCnpj($value));
  }

  /**
   * @testWith [null]
   *           [5]
   *           [8.4]
   *           [true]
   */
  public function testeValidateCnpjInvalidType($value): void
  {
    $this->expectException(\TypeError::class);
    $this->validator->validateCnpj($value);
  }
}
