<?php

namespace Up\Util\TemplateEngine;
use Up\Util\Alert;

class Template
{
	private Alert $alert;
	private string $path;
	private array $variables;

	private array $params = [
		'is_xss' => true,
		'is_nl2br' => false
	];

    public function __construct(string $path, array $variables = [])
    {
        $this->path = ROOT . '/src/View/' . $path . '.php';
        $this->variables = $variables;
    }

    public function setParam(string $param, bool $value): bool
    {
        if (isset($this->params[$param])) {
            $this->params[$param] = $value;
            return true;
        }

		return false;
	}

    public function display(): void
    {
        if (!file_exists($this->path)) {
            throw new \Exception('Template file ' . $this->path . ' not exists');
        }


        require($this->path);
    }

    private function xssProtection(mixed $variables): array|string
    {
        if (is_array($variables)) {
            $protected = [];
            foreach ($variables as $key => $value) {
                $protected[$key] = $this->xssProtection($value);
            }

            return $protected;
        }

		return htmlspecialchars($variables);
	}

    private function endOfLineToBr(mixed $variable): array|string
    {
        if (is_array($variable)) {
            $protected = [];
            foreach ($variable as $key => $value) {
                $protected[$key] = $this->endOfLineToBr($value);
            }

            return $protected;
        }

        return nl2br($variable);
    }

    //TODO XSS protection
    public function getVariable(string $name): mixed
    {
        return $this->variables[$name];
        /*if (isset($this->variables[$name]))
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

        return null;*/
    }
}
