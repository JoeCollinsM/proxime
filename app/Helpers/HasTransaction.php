<?php
namespace App\Helpers;

use App\Models\Transaction;

trait HasTransaction
{
    function transactions()
    {
        return $this->morphMany(Transaction::class, 'user');
    }
}