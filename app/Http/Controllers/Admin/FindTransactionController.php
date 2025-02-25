<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Bavix\Wallet\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon; // For date handling
class FindTransactionController extends Controller
{

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
            ->whereIn('type', ['deposit', 'withdraw']);

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
}