<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Snapshot  extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snapshot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    public function handle()
    {
        $path = app_path('Core/migrations.sql');
        touch($path);

        $command = 'mysqldump --no-defaults --no-tablespaces --user=' . config('database.connections.mysql.username') .
            ' --password=' . config('database.connections.mysql.password') .
            ' --host=' . config('database.connections.mysql.host') . ' ' . config('database.connections.mysql.database') .
            '  > ' . $path;

        exec($command);

        if (file_exists($path)) {
            $this->info('The snapshot has been processed successfully.');
        } else {
            $this->error('The snapshot process has been failed');
        }
    }
}
