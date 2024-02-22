<?php

declare(strict_types=1);

namespace Calcagno\Validator;

use DateTime;
use InvalidArgumentException;

/**
 * Class for validation of data
 *
 * @author Rafael Wendel Pinheiro (http://www.rafaelwendel.com)
 * @version 1.0
 * @link https://github.com/rafaelwendel/DataValidator/
 */
final class Validator
{
  use ValidateAttributes;

  private array $_data     = array();
  private array $_errors   = array();
  private array $_pattern  = array();
  private readonly array $messages;
  private readonly array $data;

  /**
   * Construct method (Set the error messages default)
   * @access public
   * @return void
   */
  public function __construct(array $data = [])
  {
    $this->set_messages_default();
    $this->define_pattern();
    $this->data = $data;
  }

  /**
   * Set a data for validate
   * @access public
   * @param $name String The name of data
   * @param $value Mixed The value of data
   * @return Data_Validator The self instance
   */
  public function set(string $name, mixed $value = null, string $displayName = null)
  {
    $this->_data['name'] = $name;
    $this->_data['value'] = $this->data[$name] ?? $value;
    $this->_data['display_name'] =  $displayName ?? $name;

    return $this;
  }

  /**
   * Set error messages default born in the class
   * @access protected
   * @return void
   */
  protected function set_messages_default()
  {
    $this->messages = array(
      'is_required'    => 'O campo %s é obrigatório',
      'min_length'     => 'O campo %s deve conter ao mínimo %s caracter(es)',
      'max_length'     => 'O campo %s deve conter ao máximo %s caracter(es)',
      'length'         => 'O campo %s deve conter %s caracter(es)',
      'between_length' => 'O campo %s deve conter entre %s e %s caracter(es)',
      'min_value'      => 'O valor do campo %s deve ser maior que %s ',
      'max_value'      => 'O valor do campo %s deve ser menor que %s ',
      'between_values' => 'O valor do campo %s deve estar entre %s e %s',
      'is_email'       => 'O email %s não é válido ',
      'is_url'         => 'A URL %s não é válida ',
      'is_slug'        => '%s não é um slug ',
      'is_num'         => 'O valor %s não é numérico ',
      'is_integer'     => 'O valor %s não é inteiro ',
      'is_float'       => 'O valor %s não é float ',
      'is_string'      => 'O valor %s não é String ',
      'is_boolean'     => 'O valor %s não é booleano ',
      'is_obj'         => 'A variável %s não é um objeto ',
      'is_instance_of' => '%s não é uma instância de %s ',
      'is_arr'         => 'A variável %s não é um array ',
      'is_directory'   => '%s não é um diretório válido ',
      'is_equals'      => 'O valor do campo %s deve ser igual à %s ',
      'is_not_equals'  => 'O valor do campo %s não deve ser igual à %s ',
      'is_cpf'         => 'O valor %s não é um CPF válido ',
      'is_cnpj'        => 'O valor %s não é um CNPJ válido ',
      'contains'       => 'O campo %s só aceita um do(s) seguinte(s) valore(s): [%s] ',
      'not_contains'   => 'O campo %s não aceita o(s) seguinte(s) valore(s): [%s] ',
      'is_lowercase'   => 'O campo %s só aceita caracteres minúsculos ',
      'is_uppercase'   => 'O campo %s só aceita caracteres maiúsculos ',
      'is_multiple'    => 'O valor %s não é múltiplo de %s',
      'is_positive'    => 'O campo %s só aceita valores positivos',
      'is_negative'    => 'O campo %s só aceita valores negativos',
      'is_date'        => 'A data %s não é válida',
      'is_alpha'       => 'O campo %s só aceita caracteres alfabéticos',
      'is_alpha_num'   => 'O campo %s só aceita caracteres alfanuméricos',
      'no_whitespaces' => 'O campo %s não aceita espaços em branco',
      'is_phone'       => 'O campo %s não é um telefone válido',
      'is_zipCode'     => 'O campo %s não é um CEP válido',
      'is_plate'       => 'O campo $s não é válido',
      'is_ip'          => 'O campo $s não é um ip válido'
    );
  }

  /**
   * The number of validators methods available in DataValidator
   * @access public
   * @return int Number of validators methods
   */
  public function get_number_validators_methods()
  {
    return count($this->messages);
  }

  /**
   * Define a custom error message for some method
   * @access public
   * @param String $name The name of the method
   * @param String $value The custom message
   * @return void
   */
  public function set_message($name, $value)
  {
    if (array_key_exists($name, $this->messages)) {
      $this->messages[$name] = $value;
    }
  }

  /**
   * Get the error messages
   * @access public
   * @param String $param [optional] A specific method
   * @return Mixed One array with all error messages or a message of specific method
   */
  public function get_messages($param = false)
  {
    if ($param) {
      return $this->messages[$param];
    }
    return $this->messages;
  }

  /**
   * Define the pattern of name of error messages
   * @access public
   * @param String $prefix [optional] The prefix of name
   * @param String $sufix [optional] The sufix of name
   * @return void
   */
  public function define_pattern($prefix = '', $sufix = '')
  {
    $this->_pattern['prefix'] = $prefix;
    $this->_pattern['sufix']  = $sufix;
  }

  /**
   * Set a error of the invalid data
   * @access protected
   * @param String $error The error message
   * @return void
   */
  protected function set_error($error)
  {
    $this->_errors[$this->_pattern['prefix'] . $this->_data['name'] . $this->_pattern['sufix']][] = $error;
  }

  /**
   * Verify if the current data is not null
   * @access public
   * @return Data_Validator The self instance
   */
  public function is_required()
  {
    if (!$this->validateRequired($this->_data['value'])) {
      $this->set_error(sprintf($this->messages['is_required'], $this->_data['name']));
    }
    return $this;
  }

  /**
   * Verify if the length of current value is less than the parameter
   * @access public
   * @param Int $length The value for compare
   * @return Data_Validator The self instance
   */
  public function min(int $length)
  {
    if ($this->getSize($this->_data['value']) < $length) {
      $this->set_error(sprintf($this->messages['min_length'], $this->_data['name'], $length));
    }

    return $this;
  }

  /**
   * Verify if the length of current value is more than the parameter
   * @access public
   * @param Int $length The value for compare
   * @return Data_Validator The self instance
   */
  public function max(int $length)
  {
    if ($this->getSize($this->_data['value']) > $length) {
      $this->set_error(sprintf($this->messages['max_length'], $this->_data['name'], $length));
    }

    return $this;
  }

  public function length(int $length)
  {
    if (mb_strlen($this->_data['value'] ?? '') !== $length) {
      $this->set_error(sprintf($this->messages['length'], $this->_data['display_name'], $length));
    }
    return $this;
  }

  /**
   * Verify if the length current value is between than the parameters
   * @access public
   * @param Int $min The minimum value for compare
   * @param Int $max The maximum value for compare
   * @return Data_Validator The self instance
   */
  public function between_length($min, $max)
  {
    if (strlen($this->_data['value']) < $min || strlen($this->_data['value']) > $max) {
      $this->set_error(sprintf($this->messages['between_length'], $this->_data['name'], $min, $max));
    }
    return $this;
  }

  /**
   * Verify if the current value is less than the parameter
   * @access public
   * @param Int $value The value for compare
   * @param Boolean $inclusive [optional] Include the value in the comparison
   * @return Data_Validator The self instance
   */
  public function min_value($value, $inclusive = false)
  {
    $verify = ($inclusive === true ? !is_numeric($this->_data['value']) || $this->_data['value'] >= $value : !is_numeric($this->_data['value']) || $this->_data['value'] > $value);
    if (!$verify) {
      $this->set_error(sprintf($this->messages['min_value'], $this->_data['name'], $value));
    }
    return $this;
  }

  /**
   * Verify if the current value is more than the parameter
   * @access public
   * @param Int $value The value for compare
   * @param Boolean $inclusive [optional] Include the value in the comparison
   * @return Data_Validator The self instance
   */
  public function max_value($value, $inclusive = false)
  {
    $verify = ($inclusive === true ? !is_numeric($this->_data['value']) || $this->_data['value'] <= $value : !is_numeric($this->_data['value']) || $this->_data['value'] < $value);
    if (!$verify) {
      $this->set_error(sprintf($this->messages['max_value'], $this->_data['name'], $value));
    }
    return $this;
  }

  /**
   * Verify if the length of current value is more than the parameter
   * @access public
   * @param Int $min_value The minimum value for compare
   * @param Int $max_value The maximum value for compare
   * @return Data_Validator The self instance
   */
  public function between_values($min_value, $max_value)
  {
    if (!is_numeric($this->_data['value']) || (($this->_data['value'] < $min_value || $this->_data['value'] > $max_value))) {
      $this->set_error(sprintf($this->messages['between_values'], $this->_data['name'], $min_value, $max_value));
    }
    return $this;
  }

  /**
   * Verify if the current data is a valid email
   * @access public
   * @return Data_Validator The self instance
   */
  public function is_email()
  {
    if (empty($this->_data['value'])) {
      return $this;
    }

    if (filter_var($this->_data['value'], FILTER_VALIDATE_EMAIL) === false) {
      $this->set_error(sprintf($this->messages['is_email'], $this->_data['value']));
    }
    return $this;
  }

  /**
   * Verify if the current data is a valid URL
   * @access public
   * @return Data_Validator The self instance
   */
  public function is_url()
  {
    if (filter_var($this->_data['value'], FILTER_VALIDATE_URL) === false) {
      $this->set_error(sprintf($this->messages['is_url'], $this->_data['value']));
    }
    return $this;
  }

  /**
   * Verify if the current data is a slug
   * @access public
   * @return Data_Validator The self instance
   */
  public function is_slug()
  {
    $verify = true;
    $input = $this->_data['value'];

    if (strstr($input, '--')) {
      $verify = false;
    }
    if (!preg_match('@^[0-9a-z\-]+$@', $input)) {
      $verify = false;
    }
    if (preg_match('@^-|-$@', $input)) {
      $verify = false;
    }
    if (!$verify) {
      $this->set_error(sprintf($this->messages['is_slug'], $this->_data['value']));
    }
    return $this;
  }

  /**
   * Verify if the current data is a numeric value
   * @access public
   * @return Data_Validator The self instance
   */
  public function is_num()
  {
    if (!is_numeric($this->_data['value'])) {
      $this->set_error(sprintf($this->messages['is_num'], $this->_data['value']));
    }
    return $this;
  }

  /**
   * Verify if the current data is a integer value
   * @access public
   * @return Data_Validator The self instance
   */
  public function is_integer()
  {
    if (!is_numeric($this->_data['value']) && (int) $this->_data['value'] != $this->_data['value']) {
      $this->set_error(sprintf($this->messages['is_integer'], $this->_data['value']));
    }
    return $this;
  }

  /**
   * Verify if the current data is a float value
   * @access public
   * @return Data_Validator The self instance
   */
  public function is_float()
  {
    if (!is_float(filter_var($this->_data['value'], FILTER_VALIDATE_FLOAT))) {
      $this->set_error(sprintf($this->messages['is_float'], $this->_data['value']));
    }
    return $this;
  }

  /**
   * Verify if the current data is a string value
   * @access public
   * @return Data_Validator The self instance
   */
  public function is_string()
  {
    if (!is_string($this->_data['value'])) {
      $this->set_error(sprintf($this->messages['is_string'], $this->_data['value']));
    }
    return $this;
  }

  /**
   * Verify if the current data is a boolean value
   * @access public
   * @return Data_Validator The self instance
   */
  public function is_boolean()
  {
    if (!is_bool($this->_data['value'])) {
      $this->set_error(sprintf($this->messages['is_boolean'], $this->_data['value']));
    }
    return $this;
  }

  /**
   * Verify if the current data is a object
   * @access public
   * @return Data_Validator The self instance
   */
  public function is_obj()
  {
    if (!is_object($this->_data['value'])) {
      $this->set_error(sprintf($this->messages['is_obj'], $this->_data['name']));
    }
    return $this;
  }

  /**
   * Verify if the current data is a instance of the determinate class
   * @access public
   * @param String $class The class for compare
   * @return Data_Validator The self instance
   */
  public function is_instance_of($class)
  {
    if (!($this->_data['value'] instanceof $class)) {
      $this->set_error(sprintf($this->messages['is_instance_of'], $this->_data['name'], $class));
    }
    return $this;
  }

  /**
   * Verify if the current data is a array
   * @access public
   * @return Data_Validator The self instance
   */
  public function is_arr()
  {
    if (!is_array($this->_data['value'])) {
      $this->set_error(sprintf($this->messages['is_arr'], $this->_data['name']));
    }
    return $this;
  }

  /**
   * Verify if the current parameter it is a directory
   * @access public
   * @return Data_Validator The self instance
   */
  public function is_directory()
  {
    $verify = is_string($this->_data['value']) && is_dir($this->_data['value']);
    if (!$verify) {
      $this->set_error(sprintf($this->messages['is_directory'], $this->_data['value']));
    }
    return $this;
  }

  /**
   * Verify if the current data is equals than the parameter
   * @access public
   * @param String $value The value for compare
   * @param Boolean $identical [optional] Identical?
   * @return Data_Validator The self instance
   */
  public function is_equals($value, $identical = false)
  {
    $verify = ($identical === true ? $this->_data['value'] === $value : strtolower($this->_data['value']) == strtolower($value));
    if (!$verify) {
      $this->set_error(sprintf($this->messages['is_equals'], $this->_data['name'], $value));
    }
    return $this;
  }

  /**
   * Verify if the current data is not equals than the parameter
   * @access public
   * @param String $value The value for compare
   * @param Boolean $identical [optional] Identical?
   * @return Data_Validator The self instance
   */
  public function is_not_equals($value, $identical = false)
  {
    $verify = ($identical === true ? $this->_data['value'] !== $value : strtolower($this->_data['value']) != strtolower($value));
    if (!$verify) {
      $this->set_error(sprintf($this->messages['is_not_equals'], $this->_data['name'], $value));
    }
    return $this;
  }

  /**
   * Verify if the current data is a valid CPF
   * @access public
   * @return Data_Validator The self instance
   */
  public function is_cpf()
  {
    if (!$this->validateCpf($this->_data['value'])) {
      $this->set_error(sprintf($this->messages['is_cpf'], $this->_data['value']));
    }

    return $this;
  }

  /**
   * Verify if the current data is a valid CNPJ
   * @access public
   * @return Data_Validator The self instance
   */
  public function is_cnpj()
  {
    $verify = true;

    $c = preg_replace('/\D/', '', $this->_data['value']);
    $b = array(6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2);

    if (strlen($c) != 14)
      $verify = false;
    for ($i = 0, $n = 0; $i < 12; $n += $c[$i] * $b[++$i]);

    if ($c[12] != ((($n %= 11) < 2) ? 0 : 11 - $n))
      $verify = false;

    for ($i = 0, $n = 0; $i <= 12; $n += $c[$i] * $b[$i++]);

    if ($c[13] != ((($n %= 11) < 2) ? 0 : 11 - $n))
      $verify = false;

    if (!$verify) {
      $this->set_error(sprintf($this->messages['is_cnpj'], $this->_data['value']));
    }

    return $this;
  }

  /**
   * Verify if the current data contains in the parameter
   * @access public
   * @param Mixed $values One array or String with valids values
   * @param Mixed $separator [optional] If $values as a String, pass the separator of values (ex: , - | )
   * @return Data_Validator The self instance
   */
  public function contains($values, $separator = false)
  {
    if (!is_array($values)) {
      if (!$separator || is_null($values)) {
        $values = array();
      } else {
        $values = explode($separator, $values);
      }
    }

    if (!in_array($this->_data['value'], $values)) {
      $this->set_error(sprintf($this->messages['contains'], $this->_data['name'], implode(', ', $values)));
    }
    return $this;
  }

  /**
   * Verify if the current data not contains in the parameter
   * @access public
   * @param Mixed $values One array or String with valids values
   * @param Mixed $separator [optional] If $values as a String, pass the separator of values (ex: , - | )
   * @return Data_Validator The self instance
   */
  public function not_contains($values, $separator = false)
  {
    if (!is_array($values)) {
      if (!$separator || is_null($values)) {
        $values = array();
      } else {
        $values = explode($separator, $values);
      }
    }

    if (in_array($this->_data['value'], $values)) {
      $this->set_error(sprintf($this->messages['not_contains'], $this->_data['name'], implode(', ', $values)));
    }
    return $this;
  }

  /**
   * Verify if the current data is loweracase
   * @access public
   * @return Data_Validator The self instance
   */
  public function is_lowercase()
  {
    if ($this->_data['value'] !== mb_strtolower($this->_data['value'], mb_detect_encoding($this->_data['value']))) {
      $this->set_error(sprintf($this->messages['is_lowercase'], $this->_data['name']));
    }
    return $this;
  }

  /**
   * Verify if the current data is uppercase
   * @access public
   * @return Data_Validator The self instance
   */
  public function is_uppercase()
  {
    if ($this->_data['value'] !== mb_strtoupper($this->_data['value'], mb_detect_encoding($this->_data['value']))) {
      $this->set_error(sprintf($this->messages['is_uppercase'], $this->_data['name']));
    }
    return $this;
  }

  /**
   * Verify if the current data is multiple of the parameter
   * @access public
   * @param Int $value The value for comparison
   * @return Data_Validator The self instance
   */
  public function is_multiple($value)
  {
    if ($value == 0) {
      $verify = ($this->_data['value'] == 0);
    } else {
      $verify = ($this->_data['value'] % $value == 0);
    }
    if (!$verify) {
      $this->set_error(sprintf($this->messages['is_multiple'], $this->_data['value'], $value));
    }
    return $this;
  }

  /**
   * Verify if the current data is a positive number
   * @access public
   * @param Boolean $inclusive [optional] Include 0 in comparison?
   * @return Data_Validator The self instance
   */
  public function is_positive($inclusive = false)
  {
    $verify = ($inclusive === true ? ($this->_data['value'] >= 0) : ($this->_data['value'] > 0));
    if (!$verify) {
      $this->set_error(sprintf($this->messages['is_positive'], $this->_data['name']));
    }
    return $this;
  }

  /**
   * Verify if the current data is a negative number
   * @access public
   * @param Boolean $inclusive [optional] Include 0 in comparison?
   * @return Data_Validator The self instance
   */
  public function is_negative($inclusive = false)
  {
    $verify = ($inclusive === true ? ($this->_data['value'] <= 0) : ($this->_data['value'] < 0));
    if (!$verify) {
      $this->set_error(sprintf($this->messages['is_negative'], $this->_data['name']));
    }
    return $this;
  }

  /**
   * Verify if the current data is a valid Date
   * @access public
   * @param String $format [optional] The Date format
   * @return Data_Validator The self instance
   */
  public function is_date($format = null)
  {
    $verify = true;
    if ($this->_data['value'] instanceof DateTime) {
      return $this;
    } elseif (!is_string($this->_data['value'])) {
      $verify = false;
    } elseif (is_null($format)) {
      $verify = (strtotime($this->_data['value']) !== false);
      if ($verify) {
        return $this;
      }
    }
    if ($verify) {
      $date_from_format = DateTime::createFromFormat($format, $this->_data['value']);
      $verify = $date_from_format && $this->_data['value'] === date($format, $date_from_format->getTimestamp());
    }
    if (!$verify) {
      $this->set_error(sprintf($this->messages['is_date'], $this->_data['value']));
    }
    return $this;
  }

  /**
   * Verify if the current data contains just alpha caracters
   * @access protected
   * @param String $string_format The regex
   * @param String $additional [optional] The extra caracters
   * @return Boolean True if data is valid or false otherwise
   */
  protected function generic_alpha_num($string_format, $additional = '')
  {
    $this->_data['value'] = (string) $this->_data['value'];
    $clean_input = str_replace(str_split($additional), '', $this->_data['value']);
    return ($clean_input !== $this->_data['value'] && $clean_input === '') || preg_match($string_format, $clean_input);
  }

  /**
   * Verify if the current data contains just alpha caracters
   * @access public
   * @param String $additional [optional] The extra caracters
   * @return Data_Validator The self instance
   */
  public function is_alpha($additional = '')
  {
    $string_format = '/^(\s|[a-zA-Z])*$/';
    if (!$this->generic_alpha_num($string_format, $additional)) {
      $this->set_error(sprintf($this->messages['is_alpha'], $this->_data['name']));
    }
    return $this;
  }

  /**
   * Verify if the current data contains just alpha-numerics caracters
   * @access public
   * @param String $additional [optional] The extra caracters
   * @return Data_Validator The self instance
   */
  public function is_alpha_num($additional = '')
  {
    $string_format = '/^(\s|[a-zA-Z0-9])*$/';
    if (!$this->generic_alpha_num($string_format, $additional)) {
      $this->set_error(sprintf($this->messages['is_alpha_num'], $this->_data['name']));
    }
    return $this;
  }

  /**
   * Verify if the current data no contains white spaces
   * @access public
   * @return Data_Validator The self instance
   */
  public function no_whitespaces()
  {
    $verify = is_null($this->_data['value']) || preg_match('#^\S+$#', $this->_data['value']);
    if (!$verify) {
      $this->set_error(sprintf($this->messages['no_whitespaces'], $this->_data['name']));
    }
    return $this;
  }

  /**
   * Verify if the current data is a valid Phone Number (8 or 9 digits)
   * @access public
   * @return Data_Validator The self instance
   */
  public function is_phone()
  {
    if (empty($this->_data['value'])) {
      return $this;
    }

    $verify = preg_match('/^(\(0?\d{2}\)\s?|0?\d{2}[\s.-]?)\d{4,5}[\s.-]?\d{4}$/', $this->_data['value']);
    if (!$verify) {
      $this->set_error(sprintf($this->messages['is_phone'], $this->_data['name']));
    }
    return $this;
  }

  /**
   * Verify if the current data is a valid License Plate (Brazil)
   * @access public
   * @return Data_Validator The self instance
   */
  public function is_plate()
  {
    $verify = preg_match('/^[A-Z]{3}\-[0-9]{4}$/', $this->_data['value']);
    if (!$verify) {
      $this->set_error(sprintf($this->messages['is_plate'], $this->_data['name']));
    }
    return $this;
  }

  /**
   * Verify if the current data is a valid IP
   * @access public
   * @return Data_Validator The self instance
   */
  public function is_ip()
  {
    if (filter_var($this->_data['value'], FILTER_VALIDATE_IP) === false) {
      $this->set_error(sprintf($this->messages['is_ip'], $this->_data['value']));
    }
    return $this;
  }

  /**
   * Verify if the current data is a valid Zip Code (Brazil)
   * @access public
   * @return Data_Validator The self instance
   */
  public function is_zipCode()
  {
    $verify = preg_match('/^[0-9]{5}-[0-9]{3}$/', $this->_data['value']);
    if (!$verify) {
      $this->set_error(sprintf($this->messages['is_zipCode'], $this->_data['name']));
    }
    return $this;
  }

  public function is_unique(int $quant)
  {
    $verify = $quant;
    if ($verify) {
      $this->set_error('Valor ja cadastrado');
    }
    return $this;
  }

  public function custom(callable $function, string $message): self
  {
    $value = $this->_data['value'];

    if (is_null($value) || (empty($value) && !is_int($value))) {
      return $this;
    }

    $result = $function();

    if (!is_bool($result)) {
      throw new InvalidArgumentException('A função passada deve retornar um booleano.');
    }

    if (!$result) {
      $this->set_error($message);
    }

    return $this;
  }

  public function is_file()
  {
    $error = array(
      1 => 'O arquivo enviado excede o limite definido na diretiva upload_max_filesize do php.ini.',
      2 => 'O arquivo excede o limite definido em MAX_FILE_SIZE no formulário HTML',
      3 => 'O upload do arquivo foi feito parcialmente',
      4 => 'Nenhum arquivo foi enviado',
      6 => 'Pasta temporária ausênte',
      7 => 'Falha em escrever o arquivo em disco.',
      8 => 'Uma extensão do PHP interrompeu o upload do arquivo.',
    );

    $verify = $this->_data['value']['error'];

    if ($verify) {
      $this->set_error(sprintf($error[$verify]));
    }

    return $this;
  }

  public function confirmed()
  {
    $attribute = $this->_data['name'] . '_confirmation';
    $other = $this->data[$attribute];
    $value = $this->_data['value'];

    if ($value !== $other) {
      $this->set_error("Os campos {$this->_data['name']} e {$attribute} não correspondem!");
    }

    return $this;
  }

  public function same($value)
  {
    if ($this->_data['value'] !== $value) {
      $this->set_error("O valor do campo deve ser igual á {$value}");
    }

    return $this;
  }

  public function file_extension(array $extensions)
  {
    if (is_array($extensions) && !is_null($extensions) && !empty($extensions)) {
      $ext = mb_strtolower(pathinfo($this->_data['value']['name'], PATHINFO_EXTENSION));
      if (!in_array($ext, $extensions)) {
        $this->set_error("Apenas arquivos [" . implode(", ", $extensions) . "] são permitidos!");
      }
    } else {
      $this->set_error("Parâmetros de validação não configurados");
    }
    return $this;
  }

  public function file_size(int $size)
  {
    $sizeB = 1024 * 1024 * $size;
    if ($this->_data['value']['size'] > $sizeB) {
      $this->set_error("O tamanho Maximo do arquivo deve ser {$size}MB.");
    }
    return $this;
  }


  /**
   * Validate the data
   * @access public
   * @return bool The validation of data
   */
  public function validate()
  {
    return (count($this->_errors) > 0 ? false : true);
  }


  /**
   * The messages of invalid data
   * @param String $param [optional] A specific error
   * @return Mixed One array with messages or a message of specific error
   */
  public function get_errors($param = false)
  {
    if ($param) {
      if (isset($this->_errors[$this->_pattern['prefix'] . $param . $this->_pattern['sufix']])) {
        return $this->_errors[$this->_pattern['prefix'] . $param . $this->_pattern['sufix']];
      } else {
        return false;
      }
    }
    return $this->_errors;
  }

  private function getSize(mixed $value): int
  {
    // if (is_numeric($value)) {
    //   return intval($value);
    // }

    if (is_array($value)) {
      return count($value);
    }

    // validar tamanho arquivo
    // if ($value instanceof File) {
    //   // return $value->getSize() / 1024;
    // }

    if (is_string($value)) {
      return mb_strlen($value);
    }

    return 0;
  }
}
