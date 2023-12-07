<?php

namespace App\config;

class AppParameterService
{
    private string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

}
