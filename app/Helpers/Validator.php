<?php
namespace App\Helpers;

use App\Core\Database;

class Validator
{
    private array $data;
    private array $rules;
    private array $errors = [];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public function validate(): bool
    {
        foreach ($this->rules as $field => $ruleset) {
            $value = $this->data[$field] ?? null;
            $ruleList = is_string($ruleset) ? explode('|', $ruleset) : $ruleset;

            foreach ($ruleList as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$rule, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                }

                $method = 'validate' . ucfirst($rule);
                if (method_exists($this, $method)) {
                    $passed = $this->$method($field, $value, $params);
                    if (!$passed) {
                        break; // Stop evaluating further rules for this field
                    }
                }
            }
        }

        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        foreach ($this->errors as $fieldErrors) {
            return $fieldErrors[0] ?? null;
        }
        return null;
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    private function validateRequired(string $field, mixed $value, array $params): bool
    {
        if ($value === null || (is_string($value) && trim($value) === '') || (is_array($value) && empty($value))) {
            $label = ucwords(str_replace('_', ' ', $field));
            $this->addError($field, "The {$label} field is required.");
            return false;
        }
        return true;
    }

    private function validateEmail(string $field, mixed $value, array $params): bool
    {
        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $label = ucwords(str_replace('_', ' ', $field));
            $this->addError($field, "The {$label} must be a valid email address.");
            return false;
        }
        return true;
    }

    private function validateMin(string $field, mixed $value, array $params): bool
    {
        $min = (int) ($params[0] ?? 0);
        if ($value !== null && strlen((string)$value) < $min) {
            $label = ucwords(str_replace('_', ' ', $field));
            $this->addError($field, "The {$label} must be at least {$min} characters.");
            return false;
        }
        return true;
    }

    private function validateMax(string $field, mixed $value, array $params): bool
    {
        $max = (int) ($params[0] ?? 255);
        if ($value !== null && strlen((string)$value) > $max) {
            $label = ucwords(str_replace('_', ' ', $field));
            $this->addError($field, "The {$label} cannot exceed {$max} characters.");
            return false;
        }
        return true;
    }

    private function validateNumeric(string $field, mixed $value, array $params): bool
    {
        if ($value !== null && $value !== '' && !is_numeric($value)) {
            $label = ucwords(str_replace('_', ' ', $field));
            $this->addError($field, "The {$label} must be a valid number.");
            return false;
        }
        return true;
    }

    private function validateDate(string $field, mixed $value, array $params): bool
    {
        if ($value !== null && $value !== '') {
            $d = \DateTime::createFromFormat('Y-m-d', $value);
            if (!($d && $d->format('Y-m-d') === $value)) {
                $label = ucwords(str_replace('_', ' ', $field));
                $this->addError($field, "The {$label} is not a valid date format (YYYY-MM-DD).");
                return false;
            }
        }
        return true;
    }

    private function validateIn(string $field, mixed $value, array $params): bool
    {
        if ($value !== null && !in_array((string)$value, $params, true)) {
            $label = ucwords(str_replace('_', ' ', $field));
            $this->addError($field, "The selected {$label} is invalid.");
            return false;
        }
        return true;
    }

    private function validateUnique(string $field, mixed $value, array $params): bool
    {
        if ($value === null || $value === '') {
            return true;
        }
        // table,column,exceptId,idColumn
        $table = $params[0] ?? '';
        $column = $params[1] ?? $field;
        $exceptId = $params[2] ?? null;
        $idColumn = $params[3] ?? 'id';

        $sql = "SELECT COUNT(*) AS total FROM `{$table}` WHERE `{$column}` = :val";
        $binds = [':val' => $value];

        if ($exceptId !== null && $exceptId !== '' && $exceptId !== 'NULL') {
            $sql .= " AND `{$idColumn}` != :except";
            $binds[':except'] = $exceptId;
        }

        $row = Database::fetchOne($sql, $binds);
        if (($row['total'] ?? 0) > 0) {
            $label = ucwords(str_replace('_', ' ', $field));
            $this->addError($field, "This {$label} is already taken.");
            return false;
        }
        return true;
    }
}
