<?php

namespace Easycode\Validation;

use DateTime;
use InvalidArgumentException;

class FormValidator
{
    private const DEFAULT_RULES = [
        'required', 'label', 'bool', 'email',
        'number', 'minlength', 'maxlength',
        'phone', 'ipv4', 'ipv6', 'url',
        'alphabetical', 'empty', 'min', 'max',
        'double', 'float', 'real', 'date', 'regex'
    ];
    private const BOOLEAN_VALUES = [
        true, false, 'true', 'false',
        1, 0, '1', '0', 'off', 'on',
        'yes', 'no'
    ];
    private array $data;
    private array $rules;
    private array $errors = [];

    public function __construct(array $data = null, array $rules = null)
    {
        if (!is_null($data)) {
            $this->setData($data);
        } else {
            $this->data = [];
        }

        if (!is_null($data)) {
            $this->setRules($rules);
        } else {
            $this->rules = [];
        }
    }

    /**
     * @param string|null $field
     * @return array
     */
    public function getData(string $field = null): mixed
    {
        return is_null($field) ? $this->data : $this->data[$field] ?? "The field $field not found.";
    }

    /**
     * @param array $data
     * @return FormValidator
     */
    public function setData(array $data): static
    {
        if (sizeof($data) === 0) {
            throw new InvalidArgumentException('Data cannot be empty');
        }

        $this->data = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @param array $rules
     * @return FormValidator
     */
    public function setRules(array $rules): static
    {
        if (sizeof($rules) === 0) {
            throw new InvalidArgumentException('Rules cannot be empty');
        }

        $this->rules = $rules;
        return $this;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Validate form fields with the given rules. Returns false if any error occurred.
     * @return bool
     **/
    public function validate(): bool
    {
        $this->errors = [];

        if ($this->empty($this->rules)) {
            return false;
        }

        foreach ($this->rules as $name => $rules) {
            $value = !array_key_exists($name, $this->data) ? null : $this->data[$name];

            $order = [
                'label' => $rules['label'] ?? 'Unknown',
                'required' => $rules['required'] ?? false
            ];

            $rulesOrdered = array_merge($order, $rules);

            if ($rulesOrdered['required'] === true && $this->empty($value)) {
                $this->errors[$name] = 'Please fill out the ' . $rulesOrdered['label'] . ' field.';
                $rulesOrdered = [];
            } elseif ($rulesOrdered['required'] === false && $this->empty($value)) {
                $rulesOrdered = [];
            }

            if (sizeof($rulesOrdered) > 2) {
                foreach ($rulesOrdered as $rule => $condition) {
                    if (!in_array($rule, self::DEFAULT_RULES)) {
                        $this->errors[$name] = 'The rule ' . $rule . ' not found.';
                    } else {
                        if ($rule === 'bool') {
                            if ($condition === true && !in_array($value, self::BOOLEAN_VALUES, true)) {
                                $this->errors[$name] = 'Please fill out the ' . $rulesOrdered['label'] . ' field.';
                            }
                        } elseif ($rule === 'alphabetical') {
                            $value = $this->sanitize($value);

                            if ($condition === true && !$this->alphabetical($value)) {
                                $this->errors[$name] = 'Please use only alphabetical characters for the ' . $rulesOrdered['label'] . ' field.';
                            }

                            $this->data[$name] = $value;
                        } elseif ($rule === 'email') {
                            $value = $this->sanitize($value, 'email');

                            if ($condition === true && !$this->email($value)) {
                                $this->errors[$name] = 'Please enter a valid email address.';
                            }

                            $this->data[$name] = $value;
                        } elseif (($rule === 'number') || ($rule === 'int') || ($rule === 'integer')) {
                            $value = $this->sanitize($value, 'int');

                            if ($condition === true && !is_numeric($value) && !is_int($value)) {
                                $this->errors[$name] = 'Please enter a number for the ' . $rulesOrdered['label'] . ' field.';
                            }

                            $this->data[$name] = $value;
                        } elseif (($rule === 'double') || ($rule === 'float') || ($rule === 'real')) {
                            $value = $this->sanitize($value, 'float');

                            if ($condition === true && !is_numeric($value) && !is_float($value)) {
                                $this->errors[$name] = 'Please enter a number for the ' . $rulesOrdered['label'] . ' field.';
                            }

                            $this->data[$name] = $value;
                        } elseif ($rule === 'phone') {
                            $value = $this->sanitize($value);

                            if ($condition === true && !$this->phone($value)) {
                                $this->errors[$name] = 'Please enter a valid phone number.';
                            } elseif (is_string($condition) && !$this->phone($value, $condition)) {
                                $this->errors[$name] = 'Please enter a valid phone number.';
                            }

                            $this->data[$name] = $value;
                        } elseif ($rule === 'ipv4') {
                            $value = $this->sanitize($value);

                            if ($condition === true && !$this->ipv4($value)) {
                                $this->errors[$name] = 'Please enter a number for the ' . $rulesOrdered['label'] . ' field.';
                            }

                            $this->data[$name] = $value;
                        } elseif ($rule === 'ipv6') {
                            $value = $this->sanitize($value);

                            if ($condition === true && !$this->ipv6($value)) {
                                $this->errors[$name] = 'Please enter a number for the ' . $rulesOrdered['label'] . ' field.';
                            }

                            $this->data[$name] = $value;
                        } elseif ($rule === 'url') {
                            $value = $this->sanitize($value, 'url');

                            if ($condition === true && !$this->url($value)) {
                                $this->errors[$name] = 'Please enter a url for the ' . $rulesOrdered['label'] . ' field.';
                            }

                            $this->data[$name] = $value;
                        } elseif ($rule === 'minlength') {
                            $value = $this->sanitize($value);

                            if (is_string($value)) {
                                if (is_int($condition)) {
                                    if (!$this->minlength($value, $condition)) {
                                        $this->errors[$name] = 'The ' . $rulesOrdered['label'] . ' field must be at least ' . $condition . ' characters long.';
                                    }
                                } else {
                                    $this->errors[$name] = 'The rule condition must be an integer.';
                                }
                            } else {
                                $this->errors[$name] = 'This rule only works on string data.';
                            }

                            $this->data[$name] = $value;
                        } elseif ($rule === 'maxlength') {
                            $value = $this->sanitize($value);

                            if (is_string($value)) {
                                if (is_int($condition)) {
                                    if (!$this->maxlength($value, $condition)) {
                                        $this->errors[$name] = 'The ' . $rulesOrdered['label'] . ' field must be ' . $condition . ' characters long at maximum.';
                                    }
                                } else {
                                    $this->errors[$name] = 'The rule condition must be an integer.';
                                }
                            } else {
                                $this->errors[$name] = 'This rule only works on string data.';
                            }

                            $this->data[$name] = $value;
                        } elseif ($rule === 'min') {
                            $value = $this->sanitize($value, is_int($value) ? 'int' : 'float');

                            if (is_numeric($value)) {
                                if (is_numeric($condition)) {
                                    if (!($value >= $condition)) {
                                        $this->errors[$name] = 'The ' . $rulesOrdered['label'] . ' field must be greater than ' . $condition . '.';
                                    }
                                } else {
                                    $this->errors[$name] = 'The rule condition must be an number.';
                                }
                            } else {
                                $this->errors[$name] = 'This rule only works on numeric data.';
                            }

                            $this->data[$name] = $value;
                        } elseif ($rule === 'max') {
                            $value = $this->sanitize($value, is_int($value) ? 'int' : 'float');

                            if (is_numeric($value)) {
                                if (is_numeric($condition)) {
                                    if (!($value <= $condition)) {
                                        $this->errors[$name] = 'The ' . $rulesOrdered['label'] . ' field cannot exceed ' . $condition . '.';
                                    }
                                } else {
                                    $this->errors[$name] = 'The rule condition must be an number.';
                                }
                            } else {
                                $this->errors[$name] = 'This rule only works on numeric data.';
                            }

                            $this->data[$name] = $value;
                        } elseif ($rule === 'date') {
                            $value = $this->sanitize($value);

                            if ($condition === true && !$this->dateTime($value)) {
                                $this->errors[$name] = 'Please enter a date in the format yyyy-mm-dd';
                            } elseif (is_string($condition) && !$this->dateTime($value, $condition)) {
                                $this->errors[$name] = 'Please enter a date in the format ' . $condition;
                            }

                            $this->data[$name] = $value;
                        } elseif ($rule === 'regex') {
                            if (is_string($condition) && !$this->regex($value, $condition)) {
                                $this->errors[$name] = 'Please enter a valid value.';
                            }

                            $this->data[$name] = $value;
                        }
                    }
                }
            } else {
                $this->data[$name] = $this->sanitize($value);
            }
        }

        return $this->empty($this->errors);
    }

    /**
     * Check if value is empty
     * @param string $value Value
     * @return bool
     **/
    private function empty(mixed $value): bool
    {
        return is_null($value) || (is_array($value) && sizeof($value) === 0) || (is_string($value) && trim($value) === '');
    }

    private function sanitize(mixed $value, string $type = null): mixed
    {
        return match ($type) {
            'int' => filter_var($value, FILTER_SANITIZE_NUMBER_INT),
            'float' => filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
            'email' => filter_var($value, FILTER_SANITIZE_EMAIL),
            'url' => filter_var($value, FILTER_SANITIZE_URL),
            default => filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS)
        };
    }

    /**
     * Check if value is alphabetical
     * @param string $value Value
     * @return bool
     **/
    private function alphabetical(string $value): bool
    {
        return preg_match('/^[ äöüèéàáíìóòôîêÄÖÜÈÉÀÁÍÌÓÒÔÊa-z]+$/i', $value);
    }

    /**
     * Check if value is a valid email
     * @param string $value Email
     * @return bool
     **/
    private function email(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Check if value is a valid phone number
     * @param string $value Phone
     * @param string|null $pattern
     * @return bool
     */
    private function phone(string $value, ?string $pattern = null): bool
    {
        return preg_match($pattern ?? '/\(\d{2,}\) 9\d{4,}\-\d{4}/i', $value);
    }

    /**
     * Check if value is a valid ipv4
     * @param string $value Ip
     * @return bool
     **/
    private function ipv4(string $value): bool
    {
        return $this->ip($value, FILTER_FLAG_IPV4);
    }

    /**
     * Check if value is a valid ip
     * @param string $value Ip
     * @param int|null $flag
     * @return bool
     */
    private function ip(string $value, int $flag = null): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP, $flag);
    }

    /**
     * Check if value is a valid ipv6
     * @param string $value Ip
     * @return bool
     **/
    private function ipv6(string $value): bool
    {
        return $this->ip($value, FILTER_FLAG_IPV6);
    }

    /**
     * Check if value is a valid url
     * @param string $value Url
     * @return bool
     **/
    private function url(string $value): bool
    {
        $urlScheme = parse_url($value, PHP_URL_SCHEME);

        if (is_null($urlScheme) || $urlScheme === false) {
            return false;
        }

        return in_array($urlScheme, ['http', 'https'], true);
    }

    /**
     * Check if value as a minimal length
     * @param string $value Value
     * @param int $length Length
     * @return bool
     **/
    private function minlength(string $value, int $length): bool
    {
        return strlen($value) >= $length;
    }

    /**
     * Check if value as a maximal length
     * @param string $value Value
     * @param int $length Length
     * @return bool
     **/
    private function maxlength(string $value, int $length): bool
    {
        return strlen($value) <= $length;
    }

    /**
     * Checks if it is a valid date according to the format.
     * @param string $value Value
     * @param string $format
     * @return bool
     */
    private function dateTime(string $value, string $format = 'Y-m-d'): bool
    {
        date_default_timezone_set('UTC');
        $date = DateTime::createFromFormat($format, $value);
        return $date && ($date->format($format) === $value);
    }

    /**
     * Checks if it is a valid value according to the regex.
     * @param string $value Value
     * @param string $pattern Regex
     * @return bool
     */
    private function regex(string $value, string $pattern): bool
    {
        return preg_match($pattern, $value) === true;
    }
}