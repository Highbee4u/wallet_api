<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\helper;
use Validator;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    public function index(){
        $userslist = User::get();
        if(count($userslist) > 0){
            return response()->json([
                "status" => 200,
                "data" => $userslist,
                "message" => "All users"
            ]);
        } else{
            return response()->json([
                "status" => 200,
                "data" => [],
                "message" => "No Data to display"
            ]); 
        }
    }

    public function getSingleUserdetail($email){
        if ($this->is_user_email_exists($email)) {

            $userdetail = User::where('email', $email)->get();
            $walletlists = $this->getWalletByUser($email);
            $transactionhistory = $this->gettransactionsbyuser($email);

                return response()->json([
                    "status" => 200,
                    "data" => [
                        "basic details" => $userdetail,
                        "wallet details" => $walletlists,
                        "transaction history" => $transactionhistory

                    ],
                    "message" => "User Detail"
                ]);
        } else {
                return response()->json([
                        "status" => 404,
                        "data" => [],
                        "message" => "User email supplied does not exists"
                ]);
        }
    }

    public function createuser(Request $request){
        $validator = Validator::make($request->all(), [
            'name'=>'required',
            'email'=>'required|email|unique:users,email',
            'password' =>'required|min:5|max:30',
            'cpassword'=>'required|min:5|max:30|same:password'
        ]);

        if($validator->fails()){
            return response()->json(['error'=> $validator->messages()], 401);
        }
      

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = \Hash::make($request->password);

        $save = $user->save();
        
        if($save){
            return response()->json([
                "status" => 200,
                "message" => "User Detail Save Successfully"
            ]);
        }else{
            return response()->json([
                "status" => 201,
                "message" => "Error Saving Detail, try again"
            ]);
        }

    }

   
    public function getUserWalletBalancebyemail($email){
        if($this->is_user_email_exists($email)){
            $res = DB::statement("SELECT IFNULL('wallet_balance', 0) FROM user__wallets WHERE user_email='".$email."' AND wallet_balance > '0'");
            
            if(count($res) > 0){
                return true;
            }else{
                return false;
            }
        }
    }

   

    public function getUserCount(){
        $user_count = User::all()->count();
        return response()->json([
            "status" => 200,
            "data" => $user_count,
            "message" => "Total User In the system"
        ]);
    }

    
}
