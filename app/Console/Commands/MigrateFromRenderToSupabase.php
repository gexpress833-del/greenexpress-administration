<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class MigrateFromRenderToSupabase extends Command
{
    protected $signature = 'migrate:render-to-supabase {action : export or import}';

    protected $description = 'Export data from Render PostgreSQL and import to Supabase';

    public function handle()
    {
        $action = $this->argument('action');

        if ($action === 'export') {
            return $this->exportData();
        } elseif ($action === 'import') {
            return $this->importData();
        } else {
            $this->error('Invalid action. Use: export or import');

            return Command::FAILURE;
        }
    }

    private function exportData()
    {
        $this->info('Exporting data from current database (Render)...');

        $tables = [
            'users',
            'categories',
            'meals',
            'orders',
            'order_items',
            'subscriptions',
            'subscription_suspensions',
            'deliveries',
            'commissions',
            'withdrawals',
            'delivery_reviews',
            'complaints',
            'activity_logs',
            'exchange_rates',
            'agent_points',
            'agent_rewards',
            'badges',
            'leaderboard_entries',
            'notifications',
        ];

        $exportDir = database_path('exports');
        if (! File::exists($exportDir)) {
            File::makeDirectory($exportDir, 0755, true);
        }

        $exportData = [];

        foreach ($tables as $table) {
            if (! Schema::hasTable($table)) {
                $this->warn("Table {$table} does not exist, skipping...");

                continue;
            }

            $this->info("Exporting table: {$table}");

            try {
                $data = DB::table($table)->get();
                $count = $data->count();
                $this->info("  - Exported {$count} records from {$table}");

                $exportData[$table] = $data->toArray();
            } catch (\Exception $e) {
                $this->error("  - Error exporting {$table}: ".$e->getMessage());
            }
        }

        $exportFile = $exportDir.'/render_export_'.date('Y-m-d_H-i-s').'.json';
        File::put($exportFile, json_encode($exportData, JSON_PRETTY_PRINT));

        $this->info("Export completed. Data saved to: {$exportFile}");

        return Command::SUCCESS;
    }

    private function importData()
    {
        $this->info('Importing data to current database (Supabase)...');

        $exportDir = database_path('exports');
        $files = File::files($exportDir);

        if (empty($files)) {
            $this->error('No export files found in '.$exportDir);

            return Command::FAILURE;
        }

        $latestFile = end($files);
        $this->info("Using export file: {$latestFile->getFilename()}");

        $exportData = json_decode(File::get($latestFile), true);

        if (! $exportData) {
            $this->error('Failed to read export file');

            return Command::FAILURE;
        }

        foreach ($exportData as $table => $records) {
            if (! Schema::hasTable($table)) {
                $this->warn("Table {$table} does not exist, skipping...");

                continue;
            }

            $this->info("Importing table: {$table}");

            try {
                $existingCount = DB::table($table)->count();
                if ($existingCount > 0) {
                    $this->warn("  - Table {$table} already has {$existingCount} records, skipping import");

                    continue;
                }

                foreach ($records as $record) {
                    DB::table($table)->insert((array) $record);
                }

                $this->info('  - Imported '.count($records)." records to {$table}");
            } catch (\Exception $e) {
                $this->error("  - Error importing {$table}: ".$e->getMessage());
            }
        }

        $this->info('Import completed.');

        return Command::SUCCESS;
    }
}
