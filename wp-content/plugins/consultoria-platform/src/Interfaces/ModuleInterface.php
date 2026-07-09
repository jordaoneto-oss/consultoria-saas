<?php

namespace Consultoria\Interfaces;

interface ModuleInterface {
    public function init(): void;
    public function getName(): string;
    public function getVersion(): string;
}
