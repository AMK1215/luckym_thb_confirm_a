<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Bavix\Wallet\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon; // For date handling
class FindTransactionController extends Controller
{
    public function index()
    {
        return view('admin.trans_log.tran_index');
    }

    public function getUserTransactions(Request $request)
    {
        // Validate the request
        $request->validate([
            'user_name' => 'required|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Get the user_name, start_date, and end_date from the request
        $userName = $request->input('user_name');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Find the user by user_name
        $user = User::where('user_name', $userName)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        // Start building the query
        $query = Transaction::where('payable_type', User::class)
            ->where('payable_id', $user->id)
            ->whereIn('name', ['stake', 'payout']);
            // ->whereIn('type', ['deposit', 'withdraw']);

        // Add date filters if provided
        if ($startDate) {
            $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }
        if ($endDate) {
            $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        // Get the filtered transactions
        $transactions = $query->get();

        // Pass the transactions to the view
        return view('admin.trans_log.tran_index', compact('transactions'));
    }
//     public function getUserTransactions(Request $request)
// {
//     // Validate the request
//     $request->validate([
//         'user_name' => 'required|string',
//         'start_date' => 'nullable|date',
//         'end_date' => 'nullable|date|after_or_equal:start_date',
//     ]);

//     // Get input values
//     $userName = $request->input('user_name');
//     $startDate = $request->input('start_date');
//     $endDate = $request->input('end_date');

//     // Find the user
//     $user = User::where('user_name', $userName)->first();

//     if (!$user) {
//         return response()->json(['error' => 'User not found.'], 404);
//     }

//     // Build the query with joins
//     $query = Transaction::where('payable_type', User::class)
//         ->where('payable_id', $user->id)
//         ->whereIn('name', ['stake', 'payout'])
//         ->leftJoin('bet_n_results', 'transactions.id', '=', 'bet_n_results.tran_id')
//         ->leftJoin('results', 'bet_n_results.game_code', '=', 'results.game_code')
//         ->select(
//             'transactions.*',
//             'bet_n_results.bet_amount',
//             'bet_n_results.win_amount',
//             'bet_n_results.net_win',
//             'results.game_name',
//             'results.game_provide_name'
//         );

//     // Apply date filters if provided
//     if ($startDate) {
//         $query->where('transactions.created_at', '>=', Carbon::parse($startDate)->startOfDay());
//     }
//     if ($endDate) {
//         $query->where('transactions.created_at', '<=', Carbon::parse($endDate)->endOfDay());
//     }

//     // Get the filtered transactions
//     $transactions = $query->get();

//     // Pass the transactions to the view
//     return view('admin.trans_log.tran_index', compact('transactions'));
// }

//     public function getUserTransactions(Request $request)
// {
//     // Validate the request
//     $request->validate([
//         'user_name'  => 'required|string|exists:users,user_name',
//         'start_date' => 'nullable|date',
//         'end_date'   => 'nullable|date|after_or_equal:start_date',
//     ]);

//     // Get input values
//     $userName  = $request->input('user_name');
//     $startDate = $request->input('start_date');
//     $endDate   = $request->input('end_date');

//     // Find the user
//     $user = User::where('user_name', $userName)->firstOrFail();

//     // Build the query with joins
//     $query = Transaction::where('payable_type', User::class)
//         ->where('payable_id', $user->id)
//         ->whereIn('name', ['stake', 'payout'])
//         //->join('bet_n_results', 'transactions.id', '=', 'bet_n_results.tran_id')
//         ->join('bet_n_results', 'bet_n_results.user_id', '=', 'transactions.payable_id')
//         ->join('results', 'bet_n_results.game_code', '=', 'results.game_code')
//         ->select([
//             'transactions.id',
//             'transactions.payable_id',
//             'transactions.name',
//             'transactions.amount',
//             'transactions.created_at',
//             'bet_n_results.bet_amount',
//             'bet_n_results.win_amount',
//             'bet_n_results.net_win',
//             'results.game_name',
//             'results.game_provide_name'
//         ]);

//     // Apply date filters if provided
//     if ($startDate) {
//         $query->whereDate('transactions.created_at', '>=', $startDate);
//     }
//     if ($endDate) {
//         $query->whereDate('transactions.created_at', '<=', $endDate);
//     }

//     // Get the filtered transactions
//     $transactions = $query->get();

//     // Pass the transactions to the view
//     return view('admin.trans_log.tran_index', compact('transactions'));
// }


}