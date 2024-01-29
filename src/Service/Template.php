<?php

namespace Up\Service;
class Template
{
    private $path;

    private $template;

    private $variables = [];

    private $params = [
        'xss_protection' => true,
        'exit_after_display' => true,
        'endofline_to_br' => false
    ];

    private $include_file;

    public function __construct($path = '/../Views/')
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

    public function assign(string $name, $value): void
    {
        $this->variables[$name] = $value;
    }

    public function display(string $template): void
    {
        $this->template = $this->path . $template;

        if (!file_exists($this->template))
        {
            throw new \Exception('Template file ' . $template . ' not exitst');
        }

        require_once($this->template);

        if ($this->params['exit_after_display'])
        {
            exit;
        }
    }

    private function getVariables(string $name): array|null
    {
        if (isset($this->variables[$name]))
        {
            $variable = $this->variables[$name];

            if ($this->params['xss_protection'])
            {
                $variable = $this->xssProtection($variable);
            }

            if ($this->params['endofline_to_br'])
            {
                $variable = $this->endoflineToBr($variable);
            }

            return $variable;
        }

        return null;
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
            foreach ($variable as $key => $value) {
                $protected[$key] = $this->endoflineToBr($value);
            }

            return $protected;
        }

        return nl2br($variable);
    }
}

