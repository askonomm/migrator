<?php

use PHPUnit\Framework\TestCase;

class MigratorTest extends TestCase
{
    public function testMigrate(): void
    {
        $driver = $this->createMock(\Asko\Migrator\ConnectionDriver::class);
        $driver->expects($this->once())->method('maybeSetup');
        $driver->expects($this->once())->method('migrate')->with([
            '001_test' => [
                'up' => 'TEST UP CONTENT',
                'down' => 'TEST DOWN CONTENT'
            ]
        ]);

        $migrator = new \Asko\Migrator\Migrator(__DIR__ . '/migrations', $driver);
        $migrator->migrate();
    }

    public function testMigrateNoMigrations(): void
    {
        $driver = $this->createMock(\Asko\Migrator\ConnectionDriver::class);
        $driver->expects($this->once())->method('maybeSetup');
        $driver->expects($this->once())->method('migrate')->with([]);

        $migrator = new \Asko\Migrator\Migrator(__DIR__ . '/migrations_empty', $driver);
        $migrator->migrate();
    }

    public function testRevert(): void
    {
        $driver = $this->createMock(\Asko\Migrator\ConnectionDriver::class);
        $driver->expects($this->once())->method('maybeSetup');
        $driver->expects($this->once())->method('revert')->with([
            '001_test' => [
                'up' => 'TEST UP CONTENT',
                'down' => 'TEST DOWN CONTENT'
            ]
        ]);

        $migrator = new \Asko\Migrator\Migrator(__DIR__ . '/migrations', $driver);
        $migrator->revert();
    }

    public function testRevertNoMigrations(): void
    {
        $driver = $this->createMock(\Asko\Migrator\ConnectionDriver::class);
        $driver->expects($this->once())->method('maybeSetup');
        $driver->expects($this->once())->method('revert')->with([]);

        $migrator = new \Asko\Migrator\Migrator(__DIR__ . '/migrations_empty', $driver);
        $migrator->revert();
    }
}
