<?php

namespace App\Http\Controllers;

use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class TransactionsController extends Controller
{
    public function index()
    {
        //
    }
    public function gettotaltransactionvolume(){
        
        $total_volume = DB::table('transactionsheader')
                        ->select(DB::raw('SUM(IFNULL(total_amount, 0)) as Total_transaction_volume'))
                        ->get();

        return response()->json([
            "status" => 200,
            "data" => $total_volume,
            "message" => "Total transaction Volume"
        ]);
    }

    public function sendmoney(Request $request){

        $validator = Validator::make($request->all(), [
            'sending_wallet'=>'required',
            'receiving_wallet'=>'required',
            'sender_email' =>'required|email',
            'receivers_email' =>'required|email',
            'amount' =>'required|numeric',
        ]);

        if($validator->fails()){
            return response()->json(['error'=> $validator->messages()], 401);
        }

        $sender_exists = $this->is_wallet_exists($request->sending_wallet);
        $receiver_exists = $this->is_wallet_exists($request->receiving_wallet);
        $email_exists = $this->is_user_email_exists($request->sender_email);
        
        if(!$sender_exists){
            return response()->json([
                "status" => 400,
                "message" => "Senders Wallet address suplly does not exists"
            ]);
        }
        
        if(!$receiver_exists){
            return response()->json([
                "status" => 400,
                "message" => "receivers Wallet address suplly does not exists"
            ]);
        }

        if(!$email_exists){
            return response()->json([
                "status" => 400,
                "message" => "Senders email address suplly does not exists"
            ]);
        }

        $wallet_balance = $this->getWalletBalancebywalletaddress($request->sending_wallet);
        $min_balance = $this->wallettype_min_balance($request->sending_wallet);

        // return $min_balance[0]->minimum_balance;

        $data = [
            'sending_wallet'=> $request->sending_wallet,
            'receiving_wallet'=> $request->receiving_wallet,
            'senders_email'=> $request->sender_email,
            'receivers_email'=>$request->receivers_email,
            'amount'=> $request->amount
        ];


        if(($wallet_balance[0]->wallet_balance - $request->amount) >= $min_balance[0]->minimum_balance){
                $post = $this->post_transaction('transfer', $data);

                if($post){
                    return response()->json([
                        "status" => 200,
                        "message" => "transfer successful"
                    ]);
                }else{
                    return response()->json([
                        "status" => 401,
                        "message" => "Unable to transfer, please try again later"
                    ]);
                }
        }else{
            return response()->json([
                "status" => 401,
                "message" => "Insufficient Balance"
            ]);
        }
    }


    public function post_credit($data, $trans_number){
        $posted_credit = DB::table('transactionsdetail')->insert([
            'transaction_number' => $trans_number,
            'email'=> $data['receivers_email'],
            'dramount' => 0.00,
            'cramount' => $data['amount'],
            'wallet_address' => $data['receiving_wallet'],
            'description' => 'credits for: '. $trans_number
        ]);

        if($posted_credit)
            return true;
        else
            return false;
    }

    public function post_debit($data, $trans_number){
        $posted_debit = DB::table('transactionsdetail')->insert([
            'transaction_number' => $trans_number,
            'email'=> $data['senders_email'],
            'dramount' => $data['amount'],
            'cramount' => 0.00,
            'wallet_address' => $data['sending_wallet'],
            'description' => 'debits for: '. $trans_number
        ]);

        if($posted_debit)
            return true;
        else
            return false;
    }

    public function create_header($transaction_type, $data, $trans_number){
        $res = DB::table('transactionsheader')->insert([
            'transaction_number' => $trans_number,
            'transaction_type' => $transaction_type,
            'total_amount' => $data['amount'],
            'description' => $transaction_type." - ".$trans_number
        ]);

        if($res){
            $a = $this->debit_wallet($data['sending_wallet'], $data['amount']);
            $b = $this->credit_wallet($data['receiving_wallet'], $data['amount']);
            if($a && $b) return true; else return false;
        }else
            return false;
    }

    public function get_transaction_number(){
        $permitted_chars = '0123456789';
        $transactionnumber = substr(str_shuffle($permitted_chars), 0, 04);
        
        $ret = DB::table('transactionsheader')
                    ->select('transaction_number')
                    ->where('transaction_number', $transactionnumber)
                    ->get();
        
        if(count($ret) > 0){
            $this->get_transaction_number();
        }
        return $transactionnumber;
        
        
    }

    public function post_transaction($transaction_type, $data){
       
        $trans_number = $this->get_transaction_number();

        $res = 0;
        switch($transaction_type){
            case 'transfer':
                $res = $this->create_header('transfer', $data, $trans_number);
                break;
            case 'receive':
                $res = $this->create_header('receive', $data, $trans_number);
                break;
        }

        if($res){
            $is_credit = $this->post_credit($data, $trans_number);
            $is_debit = $this->post_debit($data, $trans_number);

            if($is_credit && $is_debit) return true; else return false;
        }else{
            return false;
        }
    }

    
}
