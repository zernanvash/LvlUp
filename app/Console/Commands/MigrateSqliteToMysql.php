<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateSqliteToMysql extends Command
{
    protected $signature = 'app:sqlite-to-mysql {--fresh : Truncate MySQL tables before importing}';
    protected $description = 'Copy all user data from local SQLite into the MySQL database';

    private array $tables = [
        'users',
        'skills',
        'badges',
        'projects',
        'project_skill',
        'skill_nodes',
        'user_skill_nodes',
        'user_badges',
        'resumes',
        'daily_rewards',
        'sessions',
        'cache',
    ];

    public function handle(): int
    {
        $sqlitePath = database_path('database.sqlite');

        if (!file_exists($sqlitePath)) {
            $this->error("SQLite file not found at: {$sqlitePath}");
            return 1;
        }

        $sqlite = new \PDO('sqlite:' . $sqlitePath);
        $sqlite->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $existing = $sqlite->query(
            "SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' AND name != 'migrations'"
        )->fetchAll(\PDO::FETCH_COLUMN);

        $toMigrate = array_values(array_intersect($this->tables, $existing));
        $extras = array_diff($existing, $this->tables);
        if ($extras) {
            $this->warn('Extra tables found (appending): ' . implode(', ', $extras));
            $toMigrate = array_merge($toMigrate, array_values($extras));
        }

        $mysql = DB::connection('mysql');
        $mysql->statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($toMigrate as $table) {
            $rows = $sqlite->query("SELECT * FROM \"{$table}\"")->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($rows)) {
                $this->line("  <fg=gray>SKIP</>  {$table} (empty)");
                continue;
            }

            if ($this->option('fresh')) {
                $mysql->table($table)->truncate();
            }

            $count = 0;
            foreach (array_chunk($rows, 100) as $chunk) {
                $mysql->table($table)->insertOrIgnore($chunk);
                $count += count($chunk);
            }

            $this->info("  <fg=green>OK</>    {$table} — {$count} rows");
        }

        $mysql->statement('SET FOREIGN_KEY_CHECKS=1');
        $this->newLine();
        $this->info('Done! All data copied to local MySQL.');
        return 0;
    }
}
