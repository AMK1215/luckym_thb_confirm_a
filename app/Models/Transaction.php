<?php

namespace App\Models;

use App\Enums\TransactionName;
use App\Models\PlaceBet;
use Bavix\Wallet\Models\Transaction as ModelsTransaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends ModelsTransaction
{
    use HasFactory;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'wallet_id' => 'int',
        'confirmed' => 'bool',
        'meta' => 'json',
        'name' => TransactionName::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function wager()
    {
        return $this->belongsTo(Wager::class);
    }

    public function placeBets()
    {
        return $this->hasMany(PlaceBet::class, 'transaction_id', 'id');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class);
    }

    public function seamlessTransaction()
    {
        return $this->belongsTo(SeamlessTransaction::class);
    }

    public function scopeFilterByDate($query, $startDate = null, $endDate = null)
    {
        if ($startDate !== null && $endDate !== null) {
            $query = $query->between('created_at', [$startDate, $endDate]);
        }

        return $query;
    }
}
