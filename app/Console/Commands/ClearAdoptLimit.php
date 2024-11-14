<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\User\UserSettings;

class ClearAdoptLimit extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear-adopt-limit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resets the adoption limit for all users above 0 back to 0.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $users = UserSettings::where('adopt_limit', '>', 0)->get();

        foreach ($users as $user) {
            $user->adopt_limit = 0;
            $user->save();
        }

    }
}
