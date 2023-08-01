<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Package;
use App\Models\UserPremium;
use Illuminate\Support\Carbon;
use Midtrans\Notification;

class WebhookController extends Controller
{
    public function handler(Request $request){
        \Midtrans\Config::$isProduction = (bool)env('MIDTRANS_IS_PRODUCTION');
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        echo "Masuk 1";
        $notif = new \Midtrans\Notification();
        echo "Masuk 2";
        $transactionStatus = $notif->transaction_status;
        $transactionCode = $notif->order_id;
        $fraudStatus = $notif->fraud_status;

        if ($transactionStatus == 'capture'){
            if ($fraudStatus == 'accept'){
                $status = 'success';
            }else if($fraudStatus == 'challenge'){
                $status = 'challenge';
            }
        } else if ($transactionStatus == 'settlement'){
            $status = 'success';
        } else if ($transactionStatus == 'cancel' ||
            $transactionStatus == 'deny' || $transactionStatus == 'expire'){
            $status = 'failure';
        } else if ($notif == 'pending'){
            $status = 'pending';
        }

        $transaction = Transaction::with('package')
        ->where('transaction_code', $transactionCode)
        ->first();

        if($status == 'success'){
            $userPremium = UserPremium::where('user_id', $transaction->user_id)->first();

            if($userPremium){
                //Renewal Subscription
                $endOfSubscription = $userPremium->end_of_subscription;
                $date = Carbon::createFromFormat('Y-m-d', $endOfSubscription);
                $newEndOfSubscription = $date->addDays($transaction->package->max_days)->format(('Y-m-d'));

                $userPremium->update([
                    'package_id' => $transaction->package_id,
                    'end_of_subscription' => $newEndOfSubscription
                ]);

            }else{
                //New User Subscription
                UserPremium::create([
                    'package_id' => $transaction->package_id,
                    'user_id' => $transaction->user_id,
                    'end_of_subscription' => now()->addDays($transaction->package->max_days)
                ]);
            }


        }
        $transaction->update(['status' => $status]);
    }
}
