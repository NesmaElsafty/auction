<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Transaction;

class PaymentController extends Controller
{
    //    confirm payment
    public function confirmPayment(Request $request)
    {
        try {
        $payment = Payment::find($request->id);
        // dd($payment);
        // dd($payment->setting);
        if(!$payment){
            return response()->json(['message' => 'Payment not found'], 404);
        }
        $payment->is_paid = true;
        
        $setting = Setting::find($payment->setting_id);
        if($setting->value_type == 'percentage'){
            $amount = $payment->amount * $setting->value / 100;
        }else{
            $amount = $setting->value;
        }

        $payment->amount = $amount;
        $payment->save();

        Transaction::create([
            'model_id' => $payment->id,
            'model_type' => Payment::class,
            'amount' => $amount,
            'auction_id' => $payment->auction_id,
        ]);

        return response()->json(['message' => 'Payment confirmed successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to confirm payment', 'error' => $e->getMessage()], 500);
        }
    }
}