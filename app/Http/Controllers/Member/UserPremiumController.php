<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\UserPremium;
use App\Models\Package;
use Illuminate\Http\Request;

class UserPremiumController extends Controller
{
    public function index(){
        $userId = auth()->user()->id;
        $userPremium = UserPremium::with('package')->where('user_id', $userId)->first();

        if(!$userPremium){
            $standardPackage = Package::where('name', 'standard')->first();
            $goldPackage = Package::where('name', 'gold')->first();
            // return view('member.pricing', ['standard' => $standardPackage, 'gold' => $goldPackage]);
            return redirect()->route('member.pricing');
        }

        return view('member.subscription', ['user_premium' => $userPremium]);;
    }

    public function destroy($id){
        UserPremium::destroy($id);
        return redirect()->route('member.dashboard');
    }
}
