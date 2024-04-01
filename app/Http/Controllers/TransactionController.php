<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MergeValue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'number_account_sender' => 'required',
        'number_account_reciever' => 'required',
        'amount' => 'required|numeric|min:0'
    ]);

    
    $user = Auth::user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized user'], 401);
    }

    $senderAccount = $user->accounts->where('number_account', $request->number_account_sender)->first();

    // $senderAccount = Account::where('number_account', $request->number_account_sender)->first();
    $receiverAccount = Account::where('number_account', $request->number_account_reciever)->first();

    if (!$senderAccount || !$receiverAccount) {
        return response()->json(['message' => 'Sender or receiver account not found'], 404);
    }
                            
    if ($senderAccount->balance < $request->amount) {
        return response()->json(['message' => 'Insufficient funds'], 400);
    }

    DB::transaction(function () use ($senderAccount, $receiverAccount, $request) {
        $senderAccount->balance -= $request->amount;
        $receiverAccount->balance += $request->amount;

        $senderAccount->save();
        $receiverAccount->save();

        $transaction = new Transaction([
            'sender_account_id' => $senderAccount->id,
            'receiver_account_id' => $receiverAccount->id,
            'amount' => $request->amount
        ]);
        $transaction->save();
    });

    return response()->json(['message' => 'Transaction completed successfully'], 201);
}

    


    public function index()
    {
        $transactions = Transaction::all();
        return response()->json(['transactions' => $transactions], 200);
    }

    public function show($id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }
        return response()->json(['transaction' => $transaction], 200);
    }


   public function showUserTransactions(Request $request)
{
    $user = $request->user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized user'], 401);
    }
    
    $accountIds = $user->accounts->pluck('id')->toArray();
    $transactionsended = Transaction::whereIn('sender_account_id', $accountIds)->get();
    $transactionsrecieved = Transaction::whereIn('receiver_account_id', $accountIds)->get();
    
    $transactions = $transactionsended->merge($transactionsrecieved);
   
    return response()->json(['transactions' => $transactions], 200);

}

    


    public function destroy($id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }
        $transaction->delete();
        return response()->json(['message' => 'Transaction deleted successfully'], 200);
    }
}
