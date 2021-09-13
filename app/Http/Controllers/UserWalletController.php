<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User_Wallet;
use Illuminate\Support\Facades\DB;
use Validator;

class UserWalletController extends Controller
{

    // generate Wallet Address
    public function generate_wallet_address(){
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $address = substr(str_shuffle($permitted_chars), 0, 10);
        
        if(User_Wallet::where('wallet_address', $address)->exists()){
            $this->generate_wallet_address();
        }
        return $address;
    }

    public function index()
    {
        $walletlist = DB::table('user__wallets')->pluck('wallet_address');
        if(count($walletlist) > 0){
            return response()->json([
                "status" => 200,
                "data" => $walletlist,
                "message" => "All Wallet"
            ]);
        } else{
            return response()->json([
                "status" => 200,
                "data" => [],
                "message" => "No Wallet In the Database"
            ]); 
        }
    }

    public function getSingleWalletdetail($wallet_address)
    {
       
        if ($this->is_wallet_exists($wallet_address)) {
            $walletdetail = User_Wallet::where('wallet_address', $wallet_address)->get();
            $transactionhistory = $this->gettransactionsbywallet($wallet_address);
                return response()->json([
                    "status" => 200,
                    "data" => [
                        "wallet detail" => $walletdetail,
                        "wallet transaction history" => $transactionhistory
                    ],
                    "message" => "Wallet Detail"
                ]);
        } else {
                return response()->json([
                        "status" => 404,
                        "data" => [],
                        "message" => "Wallet Address supplied does not exists"
                ]);
        }
    }

   

    public function deleteWallet($id)
    {
        if($this->is_wallet_exists($id)) {
            if(!is_array($this->getWalletBalancebywalletaddress($id)) &&$this->getWalletBalancebywalletaddress($id) > 0){
                return response()->json([
                    "status" => 202,
                    "message" => "Wallet cannot be deleted, balance greater than zero. Move all fund and try again"
                ]);
            }else{
                $Wallet = User_Wallet::find($id);
                $Wallet->delete();
        
                return response()->json([
                    "status" => 202,
                    "message" => "records deleted"
                ]);
            }
          } else {
                return response()->json([
                "message" => "Student not found"
                ], 404);
          }
    }

    public function createwallet(Request $request){

        $validator = Validator::make($request->all(), [
            'user_email'=>'required|email',
            'wallet_type' =>'in:starter,basic,premium',
            'opening_balance'=>'required|numeric'
        ]);

        if($validator->fails()){
            return response()->json(['error'=> $validator->messages()], 401);
        }

        switch($request->wallet_type){
            case 'starter':
                if($request->opening_balance < 1000) {
                    return response()->json(['error'=> 'minimum opening balance for starter type is 1000']);
                }
                break;
            case 'basic':
                if($request->opening_balance < 2000) {
                    return response()->json(['error'=> 'minimum opening balance for basic type is 2000']);
                }
                break;
            case 'premium':
                if($request->opening_balance < 3000) {
                    return response()->json(['error'=> 'minimum opening balance for premium type is 3000']);
                }
                break;
        }

        

        if($this->is_user_email_exists($request->user_email)){
            $user_wallet = new User_Wallet();
            $user_wallet->user_email = $request->user_email;
            $user_wallet->wallet_type = $request->wallet_type;
            $user_wallet->wallet_address = $this->generate_wallet_address();
            $user_wallet->wallet_balance = $request->opening_balance;

            $save = $user_wallet->save();
            
            if($save){
                return response()->json([
                    "status" => 200,
                    "message" => "Wallet Created Successfully"
                ]);
            }else{
                return response()->json([
                    "status" => 201,
                    "message" => "Error Creating Wallet, try again"
                ]);
            }
        }else{
            return response()->json([
                "status" => 201,
                "message" => "User email Supply doesnot exists"
            ]);
        }
    }

    public function getAllWaletBalance(){
        $balances = DB::table('user__wallets')
                        ->select(DB::raw('SUM(IFNULL(wallet_balance, 0)) as Total_balance'))
                        ->get();
       
        return response()->json([
            "status" => 200,
            "data" => $balances,
            "message" => "Total Balances in all wallet In the system"
        ]);
    }

    public function getWaletCount(){
        $user_wallet_count = User_Wallet::all()->count();
        return response()->json([
            "status" => 200,
            "data" => $user_wallet_count,
            "message" => "Total count of wallet In the system"
        ]);
    }
}
