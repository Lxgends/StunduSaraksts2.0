<?php

namespace App\Observers;

use App\Models\IeplanotStundu;
use App\Models\StundaAmount;

class IeplanotStunduObserver
{
    /**
     * Handle the IeplanotStundu "created" event.
     */
    public function created(IeplanotStundu $ieplanotStundu): void
    {
        $stundaAmount = StundaAmount::where('stundaID', $ieplanotStundu->stundaID)
                                    ->where('pasniedzejsID', $ieplanotStundu->pasniedzejsID)
                                    ->where('kurssID', $ieplanotStundu->kurssID)
                                    ->first();

        if ($stundaAmount) {
            $stundaAmount->decrement('daudzums');
        }
    }

    /**
     * Handle the IeplanotStundu "updated" event.
     */
    public function updated(IeplanotStundu $ieplanotStundu): void
    {
        //
    }

    /**
     * Handle the IeplanotStundu "deleted" event.
     */
    public function deleted(IeplanotStundu $ieplanotStundu): void
    {
        //
    }

    /**
     * Handle the IeplanotStundu "restored" event.
     */
    public function restored(IeplanotStundu $ieplanotStundu): void
    {
        //
    }

    /**
     * Handle the IeplanotStundu "force deleted" event.
     */
    public function forceDeleted(IeplanotStundu $ieplanotStundu): void
    {
        //
    }
}
