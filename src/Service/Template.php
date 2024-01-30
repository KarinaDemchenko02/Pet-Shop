<?php

namespace Up\Service;
class Template
{
    private string $path;

    private string $temp;

    private array $variables = [];

    private array $params = [
        'is_xss' => true,
        'is_nl2br' => false
    ];

    private string $include_file;

    public function __construct(string $path = '/../Views/')
    {
        $this->path = __DIR__ . $path;
    }

    public function setParam(string $param, bool $value): bool
    {
        if (isset($this->params[$param]))
        {
            $this->params[$param] = $value;
            return true;
        }

        return false;
    }

    public function setIncludeFile(string $include_file): void
    {
        $this->include_file = $this->path . $include_file;

        if (!file_exists($this->path . $include_file))
        {
            throw new \Exception('Include file ' . $this->include_file . ' not exitst');
        }
    }

    public function assign(string $name, mixed $value): void
    {
        $this->variables[$name] = $value;
    }

    public function display(string $temp): void
    {
        $this->temp = $this->path . $temp;

        if (!file_exists($this->temp))
        {
            throw new \Exception('Template file ' . $temp . ' not exitst');
        }

        require_once($this->temp);
    }

    private function includeFile(): void
    {
        if (!file_exists($this->include_file))
        {
            throw new \Exception('Include file ' . $this->include_file . ' not found');
        }

        require_once($this->include_file);
    }

    private function xssProtection(mixed $variable): array|string
    {
        if (is_array($variable))
        {
            $protected = [];
            foreach ($variable as $key => $value)
            {
                $protected[$key] = $this->xssProtection($value);
            }

            return $protected;
        }

        return htmlspecialchars($variable);
    }

    private function endoflineToBr(mixed $variable): array|string
    {
        if (is_array($variable))
        {
            $protected = [];
            foreach ($variable as $key => $value)
            {
                $protected[$key] = $this->endoflineToBr($value);
            }

            return $protected;
        }

        return nl2br($variable);
    }

    private function getVariables(string $name): mixed
    {
        if (isset($this->variables[$name]))
        {
            $variable = $this->variables[$name];

            if ($this->params['is_xss'])
            {
                $variable = $this->xssProtection($variable);
            }

            if ($this->params['is_nl2br'])
            {
                $variable = $this->endoflineToBr($variable);
            }

            return $variable;
        }

        return null;
    }
}

