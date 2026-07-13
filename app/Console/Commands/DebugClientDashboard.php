<?php

namespace App\Console\Commands;

use App\Http\Controllers\Client\DashboardController;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class DebugClientDashboard extends Command
{
    protected $signature = 'debug:client-dashboard {user_id?}';

    protected $description = 'Debug client dashboard rendering and capture errors';

    public function handle(): int
    {
        $userId = $this->argument('user_id');

        if ($userId) {
            $user = User::find($userId);
        } else {
            $user = User::where('role', 'client')->first();
        }

        if (! $user) {
            $this->error('No client user found.');

            return 1;
        }

        $this->info("Debugging dashboard for user: {$user->name} (ID: {$user->id})");

        try {
            $request = Request::create('/client/dashboard', 'GET');
            $request->setUserResolver(fn () => $user);

            $controller = new DashboardController;
            $response = $controller->index($request);

            $this->info('Dashboard rendered successfully.');
            $this->info('Response type: '.get_class($response));

            return 0;
        } catch (\Throwable $e) {
            $this->error('ERROR: '.$e->getMessage());
            $this->error('File: '.$e->getFile());
            $this->error('Line: '.$e->getLine());
            $this->newLine();
            $this->error('Stack trace:');
            $this->error($e->getTraceAsString());

            return 1;
        }
    }
}
