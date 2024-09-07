<?php

declare(strict_types=1);

use Calcagno\Validator\ValidateAttributes;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

final class ValidateAttributesFullNameTest extends TestCase
{
  private ValidateAttributes $validator;

  protected function setUp(): void
  {
    $this->validator = new ValidateAttributes();
  }

  /**
   * @testWith ["josé antotônio da silva"]
   *           ["maria da conceição"]
   *           ["Joaquim José de Almeida e Silva Júnior"]
   *           ["Joana d'Arc"]
   *           ["Paulo Roberto dos Santos Neto"]
   *           ["João Carlos"]
   *           ["Márcia Lima"]
   *           ["Ana Lúcia"]
   *           ["Silvia Sá"]
   *           ["André da Costa Pereira Filho"]
   */
  public function testeValidateFullNameValid($value): void
  {
    $this->assertTrue($this->validator->validateFullName($value));
  }

  /**
   * @testWith ["ana"]
   *           ["Antonio"]
   *           ["e Carlos"]
   *           ["Márcia e"]
   *           ["Ren@ta Souz@"]
   *           ["B&er4ardo andrade"]
   *           [""]
   *           [" "]
   *           ["João"]
   *           ["Maria"]
   *           ["Alejandro Sebastián Santiago Mateo Manuel Felipe Leandro Facundo Valentín Juan Ignacio Gaspar Cristóbal Bartolomé Vicente Martín Agustín Damián Diego Antonio Bruno Bernabé Bautista Francisco Manuel Pedro Pablo Tomás Ángel Andrés Juan Diego Juan Gabriel José"]
   */
  public function testeValidateFullNameInvalid($value): void
  {
    $this->assertFalse($this->validator->validateFullName($value));
  }

  /**
   * @testWith [null]
   *           [5]
   *           [45.6]
   *           [false]
   */
  public function testeValidateFullNameInvalidType($value): void
  {
    $this->expectException(\TypeError::class);
    $this->validator->validateFullName($value);
  }
}
