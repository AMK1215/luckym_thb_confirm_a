<?php

namespace App\Http\Controllers;

use App\Enums\TransactionName;
use App\Models\Admin\AdminAddBalance;
use App\Models\Admin\UserLog;
use App\Models\SeamlessTransaction;
use App\Models\User;
use App\Services\WalletService;
use App\Settings\AppSetting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('checkBanned');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole('Senior');
        $isOwner = $user->hasRole('Owner');

        $getUserCounts = function ($roleTitle) use ($isAdmin, $user) {
            return User::whereHas('roles', function ($query) use ($roleTitle) {
                $query->where('title', '=', $roleTitle);
            })->when(! $isAdmin, function ($query) use ($user) {
                $query->where('agent_id', $user->id);
            })
                ->count();
        };

        $owner_count = $getUserCounts('Owner');
        $agent_count = $getUserCounts('Agent');
        $player_count = $getUserCounts('Player');

        return view('admin.dashboard', compact(
            'owner_count',
            'agent_count',
            'player_count',
            'user'
        ));
    }

    public function balanceUp(Request $request)
    {
        abort_if(
            Gate::denies('owner_access'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden |You cannot  Access this page because you do not have permission'
        );
        $request->validate([
            'balance' => 'required|numeric',
        ]);

        app(WalletService::class)->deposit($request->user(), $request->balance, TransactionName::CapitalDeposit);

        return back()->with('success', 'Add New Balance Successfully.');
    }

    public function logs($id)
    {
        $logs = UserLog::with('user')->where('user_id', $id)->get();

        return view('admin.logs', compact('logs'));
    }
}
