<?php

namespace App\Services;

use App\Models\Auction;
use App\Models\Agency;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;

class AuctionPaymentService
{
    public function storePayment($data, $auction, $setting)
    {
        $payment = Payment::where('auction_id', $auction->id)->where('setting_id', $setting->id)->first();
        
        $auctionAmount = $auction->end_price;
        $paymentAmount = $payment->amount;
        if($setting->value_type == 'percentage'){
            $paymentAmount = $auctionAmount * $setting->value / 100;
        }
        if($payment){
            $payment->amount = $paymentAmount;
            $payment->save();
        }
        return $paymentAmount;
    }

    public function confirmPayment($data, $auction)
    {
        $payments = Payment::where('auction_id', $auction->id)->get();
        foreach($payments as $payment){
            $payment->is_paid = true;
            $payment->save();
        }
        return true;
    }

}

