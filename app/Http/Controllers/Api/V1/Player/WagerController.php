<?php

namespace App\Http\Controllers\Api\V1\Player;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\SeamlessTransactionResource;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Traits\Purse;


class WagerController extends Controller
{
    use HttpResponses;

    public function index(Request $request)
    {
        $type = $request->get('type');

        [$from, $to] = match ($type) {
            'yesterday' => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
            'this_week' => [now()->startOfWeek(), now()->endOfWeek()],
            'last_week' => [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()],
            default => [now()->startOfDay(), now()],
        };

        $user = auth()->user();

        $transactions = $this->makeJoinTable()->select(
            'results.game_provide_name as product_name',
            DB::raw('MIN(results.tran_date_time) as from_date'),
            DB::raw('MAX(results.tran_date_time) as to_date'),
            DB::raw('COUNT(results.game_provide_name) as total_count'),
            DB::raw('SUM(results.total_bet_amount) as total_bet_amount'),
            DB::raw('SUM(results.net_win) as total_net_win_amount'))
            ->groupBy('product_name')
            ->where('results.user_id', $user->id)
            ->whereBetween('results.tran_date_time', [$from, $to])
            ->paginate();

        return $this->success(SeamlessTransactionResource::collection($transactions));
    }

    private function makeJoinTable()
    {
        $query = User::query();
        $query->join('results', 'results.user_id', '=', 'users.id');

        return $query;
    }

    use Purse;
    public function LogCheck(Request $request)
    {
        return $this->PurseService($request);
    }
}