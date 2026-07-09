<?php

namespace Consultoria\Modules;

use Consultoria\Interfaces\ModuleInterface;

abstract class BaseModule implements ModuleInterface {

    protected string $name = '';
    protected string $version = '1.0.0';
    protected bool $isActive = true;

    abstract public function init(): void;

    public function getName(): string {
        return $this->name;
    }

    public function getVersion(): string {
        return $this->version;
    }

    public function isActive(): bool {
        return $this->isActive;
    }

    public function activate(): void {
        $this->isActive = true;
    }

    public function deactivate(): void {
        $this->isActive = false;
    }

    protected function registerService(string $id, object $service): void {
        \ConsultoriaPlatform::getInstance()->registerService($id, $service);
    }

    protected function addFilter(string $tag, callable $function, int $priority = 10, int $acceptedArgs = 1): void {
        add_filter($tag, $function, $priority, $acceptedArgs);
    }

    protected function addAction(string $tag, callable $function, int $priority = 10, int $acceptedArgs = 1): void {
        add_action($tag, $function, $priority, $acceptedArgs);
    }

    protected function getDb(): \wpdb {
        global $wpdb;
        return $wpdb;
    }

    protected function getTable(string $table): string {
        return $this->getDb()->prefix . $table;
    }

    protected function log(string $message, string $level = 'info'): void {
        \Consultoria\Helpers\Logger::$level("[Module: {$this->name}] $message");
    }
}
