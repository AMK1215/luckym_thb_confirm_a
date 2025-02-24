<?php

namespace Database\Seeders;

use App\Enums\TransactionName;
use App\Enums\UserType;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $senior = $this->createUser(UserType::Senior, 'Senior', 'luckym', '09123456789');
        (new WalletService)->deposit($senior, 100 * 100_000000, TransactionName::CapitalDeposit);

        $owner = $this->createUser(UserType::Owner, 'Owner 1', 'LKM34234', '09112345678', $senior->id);
        (new WalletService)->transfer($senior, $owner, 8 * 100_0000, TransactionName::CreditTransfer);

        $agent_1 = $this->createUser(UserType::Agent, 'Agent 1', 'LMK2341354', '09112345674', $owner->id, 'vH4HueE9');
        (new WalletService)->transfer($owner, $agent_1, 1 * 100_0000, TransactionName::CreditTransfer);

        $player_1 = $this->createUser(UserType::Player, 'Player 1', 'LKM000001', '09112345674', $agent_1->id, 'h6G9we4');
        (new WalletService)->transfer($agent_1, $player_1, 1 * 100_0000, TransactionName::CreditTransfer);

        $systemWallet = $this->createUser(UserType::SystemWallet, 'SystemWallet', 'systemWallet', '09222222222');
        (new WalletService)->deposit($systemWallet, 50 * 100_0000, TransactionName::CapitalDeposit);

    }

    private function createUser(UserType $type, $name, $user_name, $phone, $parent_id = null, $referral_code = null)
    {
        return User::create([
            'name' => $name,
            'user_name' => $user_name,
            'phone' => $phone,
            'password' => Hash::make('delightmyanmar'),
            'agent_id' => $parent_id,
            'status' => 1,
            'type' => $type->value,
            'referral_code' => $referral_code,
        ]);
    }
}
