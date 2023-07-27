<?php

namespace App\Handlers;

class LFMConfigHandler
{
    public function userField()
    {
        if (auth('shop')->check()) {
            return 'shop-' . auth('shop')->user()->id;
        }
        return 'staff-' . auth('staff')->user()->id;
    }
}
