<?php


namespace App\Http\Controllers;


use App\Events\Draw;
use App\Events\NextTurn;
use App\Events\Win;
use App\Game;
use App\Pick;
use Illuminate\Http\Request;

class GameController extends Controller
{

    private $grid_size = 3;
    private $win_lines = [];

    public function play($team)
    {
        return view('play')->withTeam($team);
    }

    public function update()
    {
        return Game::first();
    }

    private function getWinningLines()
    {
        $winningLines = [];

        for ($a = 0; $a < $this->grid_size; $a++) {
            $horizontal = []; // temporary array
            $vertical = []; // temporary array
            // Iteration 2
            for ($b = 0; $b < $this->grid_size; $b++) {
                // This will append to the temporary row array the position of the row.
                $horizontal[] = $this->grid_size * $a + $b;
                // This will append to the temporary column array the position of the column
                $vertical[] = $this->grid_size * $b + $a;
            }
            // Add the generated row to the overall array.
            $winningLines['Horizontal']['Row ' . ($a + 1)] = $horizontal;
            // Add the generated column to the overall array
            $winningLines['Vertical']['Column ' . ($a + 1)] = $vertical;
            // This will append to the overall array the position of the diagonals
            $winningLines['Diagonal']['backslash'][] = $this->grid_size * $a + $a;
            // This will append to the overall array the position of the diagonals
            $winningLines['Diagonal']['forward slash'][] = $this->grid_size * ($a + 1) - ($a + 1);
        }

        return $winningLines;
    }

    private function checkGame(Game $game)
    {

        $picks = $game->picks;

        // Build picks lines
        $picksLines = [];

        foreach ($picks as $pick) {
            $picksLines[$pick->team][$pick->row][$pick->col] = $pick->team;
        }


        $lines = $this->getWinningLines();

        return $this->checkStatus($lines, $picksLines, $game->current_team);


    }

    private function checkStatus($winningLines, $currentLines, $team)
    {
        dump($team);
        dump($currentLines);
        foreach ($winningLines as $winningLineType)
        {
            dump($winningLineType);
            foreach ($winningLineType as $type) {


                $count = 0;
                foreach ($type as $row => $col) {
                    dump("$row, $col");
                    if (isset($currentLines[$team][$row][$col])) {
                        $count++;
                    }
                }
                if ($count === 3) {
                    return 'win';
                }
            }
        }

        if (Pick::all()->count() === 9) {
            return 'draw';
        }

        return 'continue';

    }

    public function pick(Request $request)
    {
        $game = $this->update();

//        dd($this->checkGame($game));

//        Pick::create([
//            'game_id' => $game->id,
//            'team' => $request->team,
//            'col' => $request->col,
//            'row' => $request->row,
//            'turn' => $game->turn_number
//        ]);



        $status = $this->checkGame($game);

        dd($status);




        ++$game->turn_number;

        if ($status === 'win') {
            event(new Win());
        } else if ($status === 'draw') {
            event(new Draw());
        } else {
            //Continue
            event(new NextTurn());
        }

        return response(['status' => $game->save()]);

    }
}
