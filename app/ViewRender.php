<?php

namespace App;

class ViewRender
{
    private string $template;
    private array $vars;

    public function __construct(string $template, array $vars = [])
    {
        $this->template = $template;
        $this->vars = $vars;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getVars(): array
    {
        return $this->vars;
    }
}