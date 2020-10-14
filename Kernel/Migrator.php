<?php


namespace InnoShop\Kernel;


use PDO;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Migrator
{
    private PDO $conn;
    private array $migrations;

    /**
     * Migrator constructor.
     * @param PDO $conn
     * @param MigrationInterface[] $migrations
     */
    public function __construct(PDO $conn, array $migrations)
    {
        $this->conn = $conn;
        $this->migrations = $migrations;
    }

    public function migrate(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $this->conn->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id VARCHAR PRIMARY KEY
            );
        ");

        $appliedMigrations = $this->conn->query("SELECT id FROM migrations")->fetchAll(PDO::FETCH_COLUMN);
        $migrationsToApply = array_filter(
            $this->migrations,
            fn(MigrationInterface $migration) => !in_array($migration->getId(), $appliedMigrations)
        );
        $insertMigrationStmt = $this->conn->prepare("INSERT INTO migrations (id) VALUES (:id)");

        if (empty($migrationsToApply)) {
            $io->success('No migrations to apply.');
            return;
        }

        foreach ($migrationsToApply as $migrationToApply) {
            $io->writeln("- Apply {$migrationToApply->getId()} ({$migrationToApply->getDescription()})");
            $migrationToApply->migrate();
            $insertMigrationStmt->execute(['id' => $migrationToApply->getId()]);
        }
        $io->success('Successfully applied migrations');
    }
}