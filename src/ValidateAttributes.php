<?php

declare(strict_types=1);

namespace Calcagno\Validator;

use InvalidArgumentException;

trait ValidateAttributes
{
  public function validateFullName(string $value)
  {
    if (is_null($value) || empty($value)) {
      return true;
    }

    $value = trim($value);

    if (mb_strlen($value, 'UTF-8') < 5 || mb_strlen($value, 'UTF-8') > 255) {
      return false;
    }

    if (!preg_match('/^[\p{L} ]+$/u', $value)) {
      return false;
    }

    $slices = explode(' ', $value);

    if (count($slices) < 2) {
      return false;
    }

    foreach ($slices as $slice) {
      if (mb_strlen($slice, 'UTF-8') < 2) {
        return false;
      }
    }

    if (mb_strlen($slices[0], 'UTF-8') < 3) {
      return false;
    }

    return true;
  }



  public function validateCpf(string $value): bool
  {
    if (empty($value)) {
      return true;
    }

    $value = preg_replace('/\D/', '', $value);

    if (mb_strlen($value) != 11) {
      return false;
    }

    return (!preg_match("/^{$value[0]}{11}$/", $value)) &&  $this->checkDigitosVerificadoresCpf($value);
  }

  private function checkDigitosVerificadoresCpf(string $value): bool
  {
    if (!ctype_digit($value)) {
      throw new InvalidArgumentException("O valor informado deve conter apenas dígitos numéricos.");
    }

    $lenght = mb_strlen($value);
    if ($lenght !== 11) {
      throw new InvalidArgumentException("O comprimento do valor informato deve se 9 ou 10.");
    }

    [$dv1, $dv2] = array_map('intval', mb_str_split(mb_substr($value, 9)));

    $dv1Valid = $this->getDigitoVerificadorCpf(mb_substr($value, 0, 9));
    $dv2Valid = $this->getDigitoVerificadorCpf(mb_substr($value, 0, 10));

    return $dv1 === $dv1Valid && $dv2 === $dv2Valid;
  }

  private function getDigitoVerificadorCpf(string $value)
  {
    if (!ctype_digit($value)) {
      throw new InvalidArgumentException("O valor informado deve conter apenas dígitos numéricos.");
    }

    $lenght = mb_strlen($value);
    if ($lenght < 9 || $lenght > 10) {
      throw new InvalidArgumentException("O comprimento do valor informato deve se 9 ou 10.");
    }

    $modulo11 = $this->modulo11($value);

    return $modulo11 < 2 ? 0 : 11 - $modulo11;
  }

  /**
   * Calcula e retorna o dígito verificador usando o algoritmo Modulo 11
   *
   * @param string $value
   * @return integer
   */
  private function modulo11(string $value): int
  {
    $soma = 0;
    $fator = 2;
    $slices = array_reverse(mb_str_split($value));

    foreach ($slices as $slice) {
      $soma += $slice * $fator;
      $fator++;
    }

    return $soma % 11;
  }

  public function validateRequired(mixed $value)
  {
    if (is_null($value)) {
      return false;
    }

    if (is_string($value) && trim($value) === '') {
      return false;
    }

    if (is_countable($value) && empty($value)) {
      return false;
    }

    return true;
  }
}
