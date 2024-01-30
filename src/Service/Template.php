<?php

namespace Up\Service;
class Template
{
    private string $path;

    private string $temp;

    private array $variables;

    private array $params = [
        'is_xss' => true,
        'is_nl2br' => false
    ];

    private string $includeFile;

    public function __construct(array $variables, string $path = '/../View/')
    {
        $this->path = __DIR__ . $path;
		$this->variables = $variables;
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
        if (!file_exists($this->path . $include_file))
        {
            throw new \Exception('Include file ' . $this->includeFile . ' not exists');
        }
		$this->includeFile = $this->path . $include_file;
    }

    public function display(string $temp): void
    {
        $this->temp = $this->path . $temp . '.php';

        if (!file_exists($this->temp))
        {
            throw new \Exception('Template file ' . $temp . ' not exists');
        }

        require_once($this->temp);
    }

    public function includeFile(): void
    {
        if (!file_exists($this->includeFile))
        {
            throw new \Exception('Include file ' . $this->includeFile . ' not found');
        }

        require_once($this->includeFile);
    }

    private function xssProtection(mixed $variables): array|string
    {
        if (is_array($variables))
        {
            $protected = [];
            foreach ($variables as $key => $value)
            {
                $protected[$key] = $this->xssProtection($value);
            }

            return $protected;
        }

        return htmlspecialchars($variables);
    }

    private function endOfLineToBr(mixed $variable): array|string
    {
        if (is_array($variable))
        {
            $protected = [];
            foreach ($variable as $key => $value)
            {
                $protected[$key] = $this->endOfLineToBr($value);
            }

            return $protected;
        }

        return nl2br($variable);
    }

    public function getVariable(string $name): mixed
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
                $variable = $this->endOfLineToBr($variable);
            }

            return $variable;
        }

        return null;
    }
}
