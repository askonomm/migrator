<?php

namespace Asko\Migrator\Drivers;

use Asko\Migrator\ConnectionDriver;
use PDOException;
use Exception;

class MysqlDriver implements ConnectionDriver
{
    private \PDO $db;

    public function __construct(
        string $host,
        string $database,
        string $username,
        string $password,
        int $port
    ) {
        $this->db = new \PDO(
            "mysql:host={$host};dbname={$database};port={$port}",
            $username,
            $password,
        );
    }

    /**
     * Get all executed migrations
     *
     * @return array<string>
     */
    private function executed(): array
    {
        $stmt = $this->db->prepare("SELECT name FROM migrations");
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_COLUMN) ?: [];
    }

    /**
     * Migrate all migrations
     * 
     * @param array<string, array<string, string>> $migrations 
     * @return void 
     * @throws PDOException 
     */
    public function migrate(array $migrations): void
    {
        $executed = $this->executed();

        foreach ($migrations as $name => ['up' => $up]) {
            if (!in_array($name, $executed)) {
                $this->db->exec($up);
                $this->db->prepare("INSERT INTO migrations (name) VALUES (?)")->execute([$name]);

                echo "Migrating: " . $name . PHP_EOL;
            }
        }
    }

    /**
     * Revert the last migration
     * 
     * @param array<string, array<string, string>> $migrations 
     * @return void 
     * @throws PDOException 
     * @throws Exception 
     */
    public function revert(array $migrations): void
    {
        $last_migration_stmt = $this->db->prepare("SELECT name FROM migrations ORDER BY id");
        $last_migration_stmt->execute();
        $last_migration = $last_migration_stmt->fetch(\PDO::FETCH_COLUMN);

        if ($last_migration) {
            $down_migration = $migrations[$last_migration]['down'] ?? null;

            if (!$down_migration) {
                throw new \Exception("Down migration not found for: {$last_migration}");
            }

            $this->db->prepare($down_migration)->execute();
            $this->db->prepare("DELETE FROM migrations WHERE name = ?")->execute([$last_migration]);

            echo "Reverting: " . $last_migration . PHP_EOL;
        }
    }

    public function maybeSetup(): void
    {
        $this->db->prepare(
            <<<SQL
                CREATE TABLE IF NOT EXISTS migrations
                (id INTEGER AUTO_INCREMENT, name VARCHAR(255) NOT NULL, PRIMARY KEY (id));
            SQL
        )->execute();
    }
}
