<?php

namespace App\Console\Commands;

use App\Models\Comment\Comment;
use Illuminate\Console\Command;

class UpdateForumComments extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-forum-comments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates forum comments to new commentable type.';

    /**
     * Create a new command instance.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $comments = Comment::where('commentable_type', 'App\Models\Forum');

        if ($comments->count()) {
            $this->line('Updating '.$comments->count().' comment commentable types...');
            $comments->update(['commentable_type' => 'App\Models\Forum\Forum']);
            $this->info('Forum comments updated!');
        } else {
            $this->line('No comments need updating!');
        }

        return;
    }
}
