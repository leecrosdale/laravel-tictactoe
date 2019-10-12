<?php

namespace App\Console\Commands;

use App\Events\NextTurn;
use App\Game;
use Illuminate\Console\Command;

class ForceTurn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:force';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Forces game to tick after x amount of time';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {


        do {

            $game = Game::first();
            if ($game->votes()->where('turn', $game->turn_number)->exists()) {
                $this->comment('End of turn!');
                event(new NextTurn(Game::first()));
            } else {
                $this->comment('Team ' . $game->current_team . ' need to pick!');
            }

            sleep(3);
        }while(true);

    }
}
