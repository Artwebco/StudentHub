<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PDO;
use Throwable;

class ImportSqliteToMysql extends Command
{
    protected $signature = 'app:import-sqlite-to-mysql {--sqlite=database/database.sqlite : Relative path to the legacy SQLite database file}';

    protected $description = 'Import business data from SQLite into the current MySQL database';

    /**
     * Tables that belong to app data and should be migrated.
     *
     * @var list<string>
     */
    private array $importOrder = [
        'users',
        'companies',
        'students',
        'lesson_types',
        'lesson_templates',
        'school_settings',
        'student_lesson_prices',
        'lessons',
        'lesson_logs',
        'invoices',
    ];

    public function handle(): int
    {
        $sqlitePath = base_path($this->option('sqlite'));

        if (!is_file($sqlitePath)) {
            $this->error("SQLite database was not found at: {$sqlitePath}");

            return self::FAILURE;
        }

        $sqlite = new PDO('sqlite:' . $sqlitePath);
        $sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sqlite->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $sqliteTables = collect($sqlite->query("SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%' ORDER BY name")->fetchAll(PDO::FETCH_COLUMN));
        $mysqlTables = collect(DB::select('SHOW TABLES'))
            ->map(fn(object $row) => (string) array_values((array) $row)[0]);

        $tables = collect($this->importOrder)
            ->filter(fn(string $table) => $sqliteTables->contains($table) && $mysqlTables->contains($table))
            ->values();

        if ($tables->isEmpty()) {
            $this->warn('No overlapping business tables were found between SQLite and MySQL.');

            return self::SUCCESS;
        }

        $this->info('Importing the following tables:');

        foreach ($tables as $table) {
            $this->line("- {$table}");
        }

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            foreach ($tables->reverse() as $table) {
                DB::table($table)->truncate();
            }

            foreach ($tables as $table) {
                $sourceColumns = collect($sqlite->query("PRAGMA table_info('{$table}')")->fetchAll())
                    ->pluck('name');
                $targetColumns = collect(Schema::getColumnListing($table));
                $columns = $targetColumns->intersect($sourceColumns)->values();

                if ($columns->isEmpty()) {
                    $this->warn("Skipped {$table}: no compatible columns found.");
                    continue;
                }

                $quotedColumns = $columns
                    ->map(fn(string $column) => '"' . str_replace('"', '""', $column) . '"')
                    ->implode(', ');

                $rows = $sqlite->query("SELECT {$quotedColumns} FROM \"{$table}\"")->fetchAll();

                if (empty($rows)) {
                    $this->line("{$table}: 0 rows imported");
                    continue;
                }

                foreach (array_chunk($rows, 250) as $chunk) {
                    DB::table($table)->insert($chunk);
                }

                $this->info("{$table}: " . count($rows) . ' rows imported');
            }
        } catch (Throwable $exception) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->info('SQLite to MySQL import completed successfully.');

        return self::SUCCESS;
    }
}
