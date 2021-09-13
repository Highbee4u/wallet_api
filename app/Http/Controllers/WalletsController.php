<?php

namespace App\Http\Controllers;

use App\Models\Wallets;
use Illuminate\Http\Request;



class WalletsController extends Controller
{
    public function index()
    {
        $wallettype = Wallets::get();
        if(count($wallettype) > 0){
            return response()->json([
                "status" => 200,
                "data" => $wallettype,
                "message" => "All Wallet Type"
            ]);
        } else{
            return response()->json([
                "status" => 200,
                "data" => [],
                "message" => "No Data to display"
            ]); 
        }
    }
    public function createWallettype(Request $request)
    {
        $Wallet = new Wallets;

        $validator = Validator::make($request->all(), [
            'wallet_type' => 'required|unique:wallets|max:255',
            'wallet_name' => 'required',
            'minimum_balance' => 'required',
            'interest_rate'=> 'required'
        ]);

        if($validator->fails()){
            return response()->json(['error'=> $validator->messages()], 401);
        }

        
        $Wallets->wallet_type = $request->wallet_type;
        $Wallets->wallet_name = $request->wallet_name;
        $Wallets->minimum_balance = $request->minimum_balance;
        $Wallets->interest_rate = $request->interest_rate;
            
        $Wallets->save();
        
            return response()->json([
                "status" => 201,
                "message" => "Wallets Type Created"
            ]);
    }

    public function getSingleWallettype($id)
    {
        if (Wallets::where('id', $id)->exists()) {
            $wallet = Wallets::where('id', $id)->get();
                return response()->json([
                    "status" => 200,
                    "data" => $wallet,
                    "message" => "Wallet Type Detail"
                ]);
        } else {
                return response()->json([
                        "status" => 404,
                        "data" => [],
                        "message" => "Wallet type supplied is does not exists"
                ]);
        }
    }

    public function updateWallettype(Request $request, $id)
    {
        if (Wallets::where('id', $id)->exists()) {
            $wallet = Wallets::find($id);
            $wallet->wallet_type = is_null($request->wallet_type) ? $wallet->wallet_type : $request->wallet_type;
            $wallet->wallet_name = is_null($request->wallet_name) ? $wallet->wallet_name : $request->wallet_name;
            $wallet->minimum_balance = is_null($request->minimum_balance) ? $wallet->minimum_balance : $request->minimum_balance;
            $wallet->interest_rate = is_null($request->interest_rate) ? $wallet->interest_rate : $request->interest_rate;
           
            $wallet->save();
    
            return response()->json([
                "status" => 200,
                "message" => "Wallet Type Detail Updated"
            ]);
        } else {
            return response()->json([
                "status" => 404,
                "message" => "Wallet Type ID Does not exist on the system"
            ]);
            
        }  
    }

    public function deleteWallettype($id)
    {
        if(Wallets::where('id', $id)->exists()) {
            $Wallet = Wallets::find($id);
            $Wallet->delete();
    
            return response()->json([
                "status" => 202,
                "message" => "records deleted"
            ]);
          } else {
            return response()->json([
              "message" => "Student not found"
            ], 404);
          }
    }

    

}
