<?php

namespace App\Listeners;

use App\Events\Draw;
use App\Events\NextTurn;
use App\Events\Win;
use App\Game;
use App\Pick;
use App\Vote;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class CheckGame
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(NextTurn $event)
    {

        $game = $event->game;

        // Decide on vote
        $this->decideVote($game);

        $game = Game::first();

        $status = $this->checkGame($game);

        dump($status);

        ++$game->turn_number;

        if ($status === 'win') {
            event(new Win());
        } else if ($status === 'draw') {
            event(new Draw());
        }

        return response(['status' => $game->save()]);

    }

    private function decideVote(Game $game)
    {
        $votes = DB::table('votes')->select(['row','col',DB::raw('COUNT(id) as vote_count')])->where('game_id', $game->id)->where('turn', $game->turn_number)->groupBy(['row','col'])->get();

        $highest_vote = 0;
        $winning_vote = null;
        foreach($votes as $vote) {
            if ($vote->vote_count > $highest_vote) {
                $highest_vote = $vote->vote_count;
                $winning_vote = $vote;
            }
        }

        if ($winning_vote) {


            Pick::create([
                'game_id' => $game->id,
                'team' => $game->current_team,
                'col' => $winning_vote->col,
                'row' => $winning_vote->row,
                'turn' => $game->turn_number
            ]);

        }

    }

    private function checkGame(Game $game)
    {

        $picks = $game->picks;

        // Build picks lines
        $picksLines = [];

        foreach ($picks as $pick) {
            $picksLines[$pick->team][$pick->row][$pick->col] = $pick->team;
        }

        dump($picksLines);

        dump($game->current_team);

        return $this->checkStatus($picksLines, $game->current_team);


    }

    private function checkStatus($currentLines, $team)
    {

        if (isset($currentLines[$team])) {
            // Horizontal
            if (isset($currentLines[$team][0][0]) && isset($currentLines[$team][0][1]) && isset($currentLines[$team][0][2])) return 'win';
            if (isset($currentLines[$team][1][0]) && isset($currentLines[$team][1][1]) && isset($currentLines[$team][1][2])) return 'win';
            if (isset($currentLines[$team][2][0]) && isset($currentLines[$team][2][1]) && isset($currentLines[$team][2][2])) return 'win';

            // Vertical
            if (isset($currentLines[$team][0][0]) && isset($currentLines[$team][1][0]) && isset($currentLines[$team][2][0])) return 'win';
            if (isset($currentLines[$team][0][1]) && isset($currentLines[$team][1][1]) && isset($currentLines[$team][2][1])) return 'win';
            if (isset($currentLines[$team][0][2]) && isset($currentLines[$team][1][2]) && isset($currentLines[$team][2][2])) return 'win';


            // Diagonal
            if (isset($currentLines[$team][0][0]) && isset($currentLines[$team][1][1]) && isset($currentLines[$team][2][2])) return 'win';
            if (isset($currentLines[$team][0][2]) && isset($currentLines[$team][1][1]) && isset($currentLines[$team][2][0])) return 'win';
        }



        if (Pick::all()->count() === 9) {
            return 'draw';
        }

        return 'continue';

    }



}
