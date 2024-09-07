<?php

declare(strict_types=1);

namespace Calcagno\Validator;

use function PHPUnit\Framework\isNull;

final class ValidateAttributes
{
  public function validateCpf($value): bool
  {
    if (!is_string($value)) {
      throw new \TypeError('Invalid type provided for CPF. Expected string.');
    }

    if (empty($value) || $value == null) {
      return true;
    }

    $value = preg_replace('/\D/', '', $value);

    if (mb_strlen($value) != 11 || preg_match("/^{$value[0]}{11}$/", $value)) {
      return false;
    }

    $tempValue = mb_substr($value, 0, 9);
    $dv = $this->modulo11($tempValue);

    $tempValue = $tempValue . strval($dv);
    $dv = $this->modulo11($tempValue);

    return  $value === ($tempValue . strval($dv));
  }

  public function validateCnpj($value): bool
  {
    if (!is_string($value)) {
      throw new \TypeError('Invalid type provided for CPF. Expected string.');
    }

    if (empty($value) || $value == null) {
      return true;
    }

    $value = preg_replace('/\D/', '', $value);

    if (mb_strlen($value) != 14 || preg_match("/^{$value[0]}{14}$/", $value)) {
      return false;
    }

    $tempValue = mb_substr($value, 0, 12);
    $dv = $this->modulo11($tempValue, 9);

    $tempValue = $tempValue . strval($dv);
    $dv = $this->modulo11($tempValue, 9);

    return  $value === ($tempValue . strval($dv));
  }

  private function modulo11($value, int $base = 11): int
  {
    $soma = 0;
    $fator = 2;
    $slices = array_reverse(mb_str_split($value));

    foreach ($slices as $slice) {
      $soma += $slice * $fator;
      $fator++;

      if ($fator > $base) {
        $fator = 2;
      }
    }

    $mod = $soma % 11;

    return $mod < 2 ? 0 : 11 - $mod;
  }

  public function validateFullName($value): bool
  {
    $value = trim($value);

    if (is_null($value) || empty($value)) {
      return false;
    }

    $length = mb_strlen($value);

    if (!preg_match('/^[\p{L}\s\']+$/u', $value) || $length < 5 || $length > 255) {
      return false;
    }

    $slices = explode(' ', $value);

    return count($slices) >= 2 && mb_strlen(reset($slices)) >= 3 && mb_strlen(end($slices)) >= 2;
  }

  public function validateRequired($value): bool
  {
    if (is_null($value)) {
      return false;
    }

    if ((is_string($value) || is_countable($value)) && empty($value)) {
      return false;
    }

    return (is_numeric($value)) ? (bool)$value : true;
  }

  public function validateMin($value, $min): bool
  {
    return $this->getSize($value) >= $min;
  }

  public function validateMax($value, $max): bool
  {
    return $this->getSize($value) <= $max;
  }

  public function validateBetween($value, $min, $max): bool
  {
    return $this->validateMin($value, $min)  && $this->validateMax($value, $max);
  }

  private function getSize($value)
  {
    if (is_null($value)) {
      throw new \TypeError('Invalid type provided.');
    }

    if (is_string($value)) {
      return mb_strlen($value ?? '');
    }

    if (is_array($value)) {
      return count($value);
    }

    return $value;
  }

  public function validateTelefone($value): bool
  {
    if (!is_string($value)) {
      throw new \TypeError('Invalid type provided for Telefone. Expected string.');
    }

    if (is_null($value) || empty($value)) {
      return true;
    }

    $normalizedValue = trim(preg_replace('/\D/', '', $value));

    if (mb_strlen($normalizedValue) < 10 || mb_strlen($normalizedValue) > 13) {
      return false;
    }

    $pattern = '/^(\(0?\d{2}\)\s?|0?\d{2}[\s.-]?)\d{4,5}[\s.-]?\d{4}$/';

    return (bool)preg_match($pattern, $value);
  }
}
