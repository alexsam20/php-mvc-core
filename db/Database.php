<?php

namespace app\core\db;

use app\core\Application;

/**
 * Class Database
 * @package app\core\db
 */
class Database
{
    public \PDO $pdo;

    /**
     * Database constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
            $dsn = $config['dsn'] ?? '';
            $user = $config['user'] ?? '';
            $password = $config['password'] ?? '';

        try {
            $this->pdo = new \PDO($dsn, $user, $password);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch(\PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function applyMigrations(): void
    {
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();

        $newMigrations = [];
        $files = scandir(Application::$ROOT_DIR . '/migrations');
        $toApplyMigrations = array_diff($files, $appliedMigrations);
        foreach ($toApplyMigrations as $migration) {
            if ($migration ==='.' || $migration === '..') {
                continue;
            }
            require_once Application::$ROOT_DIR . '/migrations/' . $migration;
            $className = pathinfo($migration, PATHINFO_FILENAME);
            $instance = new $className();
            $this->log("Applying migration $migration");
            $instance->up();
            $this->log("Applied migration $migration");
            $newMigrations[] = $migration;
        }

        if (!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        } else {
            $this->log('All migrations are applied');
        }
    }

    public function createMigrationsTable(): void
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )  ENGINE=INNODB;");
    }

    public function getAppliedMigrations(): array
    {
        $statement = $this->pdo->prepare("SELECT migration FROM migrations;");
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function saveMigrations(array $migrations): void
    {
        $str = implode(',', array_map(fn($m) => "('$m')", $migrations));
        $statement = $this->pdo->prepare("INSERT INTO migrations (migration) VALUE
            $str        
        ");
        $statement->execute();
    }

    /**
     * @param $sql
     * @return bool|\PDOStatement
     */
    public function prepare($sql)
    {
        return $this->pdo->prepare($sql);
    }

    protected function log($message): void
    {
        echo '[' . date('Y-m-d H:i:s') . '] - ' . $message . PHP_EOL;
    }
}