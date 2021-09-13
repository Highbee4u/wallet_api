<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

use App\Models\Transactions;
use App\Models\User_Wallet;
use App\Models\User;

use Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function gettransactionsbyuser($email){
        $res = DB::table('transactionsdetail')
                    ->select('*')
                    ->where('email', $email)
                    ->get();

        if(count($res) > 0)
            return $res;
        else
            return [];
    }
    
    
    function getWalletByUser($email){
        $res = User_Wallet::where('user_email', $email)->get();
        if(count($res) > 0)
            return $res;
        else
            return [];
    }

    function updateWalletbalance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wallet_address'=>'required',
            'wallet_balance'=>'required|numeric',
        ]);

        if($validator->fails()){
            return response()->json(['error'=> $validator->messages()], 401);
        }
      
        if ($this->is_exists($wallet_address)) {
            $wallet = User_Wallet::find($wallet_address);
            if(is_numeric($request->wallet_balance) && !empty($request->wallet_balance)){
                $wallet->wallet_balance = is_null($request->wallet_balance) ? $wallet->wallet_balance : $request->wallet_balance;
            
                $wallet->save();
                
                return true;
            }else{
                return response()->json([
                    "status" => 200,
                    "message" => "Balance Should be Number"
                ]);
            }
        } else {
            return response()->json([
                "status" => 404,
                "message" => "Unable to update Walet, Wallet Doesnt exists"
            ]);
            
        }  
    }

    function is_wallet_exists($wallet_address){
        if(User_Wallet::where('wallet_address', $wallet_address)->exists())
            return true;
        else    
            return false;
    }

    function is_user_email_exists($email){
        if(User::where('email', $email)->exists()){
            return true;
        }else{
            return false;
        }
    }

    function getWalletBalancebywalletaddress($wallet_address){

        $balance = DB::table('user__wallets')
                        ->select(DB::raw('IFNULL(wallet_balance, 0) as wallet_balance'))
                        ->where('wallet_address', $wallet_address)
                        ->get();
        
        return $balance;
    }

    function wallettype_min_balance($wallet_address){

        $type = DB::table('user__wallets')
                    ->select('wallet_type')
                    ->where('wallet_address', $wallet_address)
                    ->get();
        
        $balance = DB::table('wallets')
                        ->select('minimum_balance')
                        ->where('wallet_type', $type->pluck('wallet_type'))
                        ->get();

        return $balance;
    }

    function gettransactionsbywallet($wallet_address){
        $res = DB::table('transactionsdetail')
                    ->select('*')
                    ->where('wallet_address', $wallet_address)
                    ->get();

        if(count($res) > 0)
        return $res;
        else
        return [];
    }
    function debit_wallet($wallet_address, $amount){
        $cur_value = DB::table('user__wallets')
                        ->Select('wallet_balance')
                        ->where('wallet_address', $wallet_address)
                        ->get();

        $new_val = ($cur_value[0]->wallet_balance - $amount);

        $response = DB::table('user__wallets')
                        ->where('wallet_address', $wallet_address)
                        ->update(['wallet_balance' => $new_val]);
        
        if($response)
            return true;
        else
            return false;

    }

    function credit_wallet($wallet_address, $amount){
        $cur_value = DB::table('user__wallets')
                        ->Select('wallet_balance')
                        ->where('wallet_address', $wallet_address)
                        ->get();

        $new_val = ($cur_value[0]->wallet_balance + $amount);

        $response = DB::table('user__wallets')
                        ->where('wallet_address', $wallet_address)
                        ->update(['wallet_balance' => $new_val]);
        
        if($response)
            return true;
        else
            return false;

    }

}
