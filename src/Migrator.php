<?php

namespace Asko\Migrator;

readonly class Migrator
{
    public function __construct(
        private string $migrations_path,
        private ConnectionDriver $driver
    ) {
    }

    /**
     * Run all migrations
     *
     * @return void
     */
    public function migrate(): void
    {
        $this->driver->maybeSetup();
        $this->driver->migrate($this->migrations());
    }

    /**
     * Revert the last migration
     *
     * @return void
     * @throws \Exception
     */
    public function revert(): void
    {
        $this->driver->maybeSetup();
        $this->driver->revert($this->migrations());
    }

    /**
     * Get all migrations
     *
     * @return array<string, array<string, string>>
     */
    private function migrations(): array
    {
        $migrations = [];

        foreach (glob($this->migrations_path . "/*.sql") as $file) {
            $parts = explode("/", $file);
            $name = explode(".", $parts[count($parts) - 1])[0];
            $migrations[$name] = [
                'up' => file_get_contents("{$this->migrations_path}/{$name}.up.sql"),
                'down' => file_get_contents("{$this->migrations_path}/{$name}.down.sql")
            ];
        }

        return $migrations;
    }
}
