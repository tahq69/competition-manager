<?php

use Illuminate\Database\Seeder;

/**
 * Class DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{
    /**
     * List of tables to ignore when seeding is truncating database data.
     * @var array
     */
    private $ignoreTables = [
        'oauth_access_tokens',
        'oauth_auth_codes',
        'oauth_clients',
        'oauth_personal_access_clients',
        'oauth_refresh_tokens',
    ];

    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        $this->cleanDatabase();

        Eloquent::unguard();

        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(CompetitionsTableSeeder::class);

        Eloquent::unguard(false);
    }

    private function cleanDatabase()
    {
        // Get list of all tables in our database
        $tables = DB::table('information_schema.TABLES')
            ->where('TABLE_SCHEMA', env('DB_DATABASE'))
            ->where('TABLE_NAME', '<>', 'migrations')
            ->get(['TABLE_NAME']);

        // Disable FOREIGN_KEY_CHECKS to be able truncate tables with relationships
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        // Truncate database tables
        foreach ($tables as $table) {
            if (!in_array($table->TABLE_NAME, $this->ignoreTables)) {
                DB::table($table->TABLE_NAME)->truncate();
            }
        }

        // Return back FOREIGN_KEY_CHECKS to avoid future issues with missing data
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
