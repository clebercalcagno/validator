<?php

declare(strict_types=1);

namespace Tests;

use Calcagno\Validator\ValidateAttributes;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

final class ValidateAttributesTelefoneTest extends TestCase
{
  private ValidateAttributes $validator;

  protected function setUp(): void
  {
    $this->validator = new ValidateAttributes();
  }

  /**
   * @testWith ["(023) 94560-7890"]
   *           ["(28) 94560-7890"]
   *           ["(023) 4560-7890"]
   *           ["(28) 4560-7890"]
   *           ["011 1234 5678"]
   *           ["011 91234 5678"]
   *           ["27 1234 5678"]
   *           ["27 91234 5678"]
   */
  public function testeValidateTelefoneValid($value): void
  {
    $this->assertTrue($this->validator->validateTelefone($value));
  }

  /**
   * @testWith ["12345"]
   *           ["abcdefghij"]
   *           ["123-456-78901234"]
   *           ["123 456 78 90"]
   *           ["123-4567-890"]
   *           ["+1-800-555-0199x123"]
   *           ["555-0199"]
   *           ["123-45-6789"]
   *           ["123.456.789O"]
   */
  public function testeValidateTelefoneInvalid($value): void
  {
    $this->assertFalse($this->validator->validateTelefone($value));
  }

  /**
   * @testWith [null]
   *           [5]
   *           [8.4]
   *           [true]
   */
  public function testeValidateTelefoneInvalidType($value): void
  {
    $this->expectException(\TypeError::class);
    $this->validator->validateTelefone($value);
  }
}
