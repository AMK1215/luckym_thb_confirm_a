<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait AuthorizedCheck
{
    protected function FeaturePermission($agentId)
    {
        $user = Auth::user();
        $owner = $user->hasRole('Owner');

        $isAuthorized = $owner ? in_array($agentId, $user->agents()->pluck('id')->toArray()) : $user->id === $agentId;
        if ($isAuthorized) {
            return true;
        } else {
            abort(403, 'Unauthorized');
        }
    }

    protected function OwnerAgentRoleCheck()
    {
        $user = Auth::user();
        $owner_access = $user->hasPermission('owner_access');
        $agent_access = $user->hasPermission('agent_access');
        if ($owner_access || $agent_access) {
            return true;
        } else {
            abort(403, 'Unauthorized');
        }
    }
}