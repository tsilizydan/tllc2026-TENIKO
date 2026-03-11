<?php
declare(strict_types=1);
namespace App\Core;

class Request
{
    private array $params = [];

    public function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function uri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        // Strip query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        return '/' . ltrim(rawurldecode($uri), '/');
    }

    public function get(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) return $_GET;
        return $_GET[$key] ?? $default;
    }

    public function post(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) return $_POST;
        return $_POST[$key] ?? $default;
    }

    public function input(string $key = null, mixed $default = null): mixed
    {
        $data = array_merge($_GET, $_POST);
        if ($key === null) return $data;
        return $data[$key] ?? $default;
    }

    public function file(string $key): array|null
    {
        return $_FILES[$key] ?? null;
    }

    public function isAjax(): bool
    {
        return ($this->header('X-Requested-With') === 'XMLHttpRequest')
            || str_contains($this->header('Accept') ?? '', 'application/json');
    }

    public function isPost(): bool  { return $this->method() === 'POST'; }
    public function isGet(): bool   { return $this->method() === 'GET'; }

    public function header(string $name): ?string
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $_SERVER[$key] ?? null;
    }

    public function ip(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function param(string $key, mixed $default = null): mixed
    {
        return $this->params[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($_GET, $_POST, $this->params);
    }

    /**
     * Validate input fields. Returns array of errors (empty = valid).
     */
    public function validate(array $rules): array
    {
        $errors = [];
        foreach ($rules as $field => $ruleStr) {
            $value = $this->input($field);
            $ruleList = explode('|', $ruleStr);
            foreach ($ruleList as $rule) {
                if ($rule === 'required' && empty($value)) {
                    $errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
                } elseif (str_starts_with($rule, 'min:') && strlen((string)$value) < (int)substr($rule, 4)) {
                    $min = substr($rule, 4);
                    $errors[$field][] = ucfirst($field) . " must be at least {$min} characters.";
                } elseif (str_starts_with($rule, 'max:') && strlen((string)$value) > (int)substr($rule, 4)) {
                    $max = substr($rule, 4);
                    $errors[$field][] = ucfirst($field) . " must not exceed {$max} characters.";
                } elseif ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = 'Please enter a valid email address.';
                }
            }
        }
        return $errors;
    }
}
