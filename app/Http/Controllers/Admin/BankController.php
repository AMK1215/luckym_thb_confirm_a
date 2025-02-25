<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Bank;
use App\Models\BankAgent;
use App\Models\PaymentType;
use App\Traits\AuthorizedCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use AuthorizedCheck;

    public function index()
    {
        $auth = auth()->user();
        $this->OwnerAgentRoleCheck();
        $banks = $auth->hasPermission('owner_access') ?
            Bank::query()->owner()->latest()->get() :
            Bank::query()->agent()->latest()->get();

        return view('admin.banks.index', compact('banks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->OwnerAgentRoleCheck();
        $payment_types = PaymentType::all();

        return view('admin.banks.create', compact('payment_types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->OwnerAgentRoleCheck();
        $user = Auth::user();
        $isOwner = $user->hasRole('Owner');

        // Validate the request
        $request->validate([
            'account_name' => 'required',
            'account_number' => 'required|numeric',
            'payment_type_id' => 'required|exists:payment_types,id',
            'type' => $isOwner ? 'required' : 'nullable',
            'agent_id' => ($isOwner && $request->type === 'single') ? 'required|exists:users,id' : 'nullable',
        ]);

        $type = $request->type ?? 'single';
        if ($type === 'single') {
            $agentId = $isOwner ? $request->agent_id : $user->id;
            $this->FeaturePermission($agentId);
            $bank = Bank::create([
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'payment_type_id' => $request->payment_type_id,
            ]);
            BankAgent::create([
                'bank_id' => $bank->id,
                'agent_id' => $agentId,
            ]);

        } elseif ($type === 'all') {
            $bank = Bank::create([
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'payment_type_id' => $request->payment_type_id,
            ]);
            foreach ($user->agents as $agent) {
                BankAgent::create([
                    'bank_id' => $bank->id,
                    'agent_id' => $agent->id,
                ]);
            }
        }

        return redirect(route('admin.banks.index'))->with('success', 'New userPayment Added.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Bank $bank) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bank $bank)
    {
        $this->OwnerAgentRoleCheck();
        if (! $bank) {
            return redirect()->back()->with('error', 'Bank Not Found');
        }
        $payment_types = PaymentType::all();

        return view('admin.banks.edit', compact('bank', 'payment_types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bank $bank)
    {
        $this->OwnerAgentRoleCheck();
        $user = Auth::user();
        $isOwner = $user->hasRole('Owner');
        if (! $bank) {
            return redirect()->back()->with('error', 'Bank Not Found');
        }
        $data = $request->validate([
            'account_name' => 'required',
            'account_number' => 'required|numeric',
            'payment_type_id' => 'required|exists:payment_types,id',
        ]);
        $bank->update($data);

        if ($request->type === 'single') {
            $agentId = $isOwner ? $request->agent_id : $user->id;
            $bank->bankAgents()->delete();
            BankAgent::create([
                'agent_id' => $agentId,
                'bank_id' => $bank->id,
            ]);
        } elseif ($request->type === 'all') {
            foreach ($user->agents as $agent) {
                $bank->bankAgents()->updateOrCreate(
                    ['agent_id' => $agent->id],
                    ['bank_id' => $bank->id]
                );
            }
        }

        return redirect(route('admin.banks.index'))->with('success', 'Bank Updated Successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bank $bank)
    {
        $this->OwnerAgentRoleCheck();
        if (! $bank) {
            return redirect()->back()->with('error', 'Bank Not Found');
        }
        $bank->delete();

        return redirect()->back()->with('success', 'Bank Deleted Successfully.');
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:banks,id',
            'status' => 'required|boolean',
        ]);

        $item = Bank::find($request->id);
        $item->status = $request->status;
        $item->save();

        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }
}
