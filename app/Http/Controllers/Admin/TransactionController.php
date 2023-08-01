<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index(){
        $transactions = Transaction::with(['package','user'])->get();
        return view('admin.transactions', ['transactions' => $transactions]);
    }
}
