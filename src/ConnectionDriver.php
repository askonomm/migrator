<?php

namespace Asko\Migrator;

interface ConnectionDriver
{
    public function __construct(
        string $host,
        string $database,
        string $username,
        string $password,
        int $port
    );

    /**
     * @param array<string, array<string, string>> $migrations 
     * @return void 
     */
    public function migrate(array $migrations): void;

    /**
     * @param array<string, array<string, string>> $migrations 
     * @return void 
     */
    public function revert(array $migrations): void;

    public function maybeSetup(): void;
}
