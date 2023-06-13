<?php

namespace App\Http\Controllers\API;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MidtransController extends Controller
{
    public function notification(Request $request)
    {
        // Parse the request body as JSON
        $payload = json_decode($request->getContent());

        // You can now access the properties of the payload using the arrow operator ->
        $transactionStatus = $payload->transaction_status;
        $fraudStatus = $payload->fraud_status;
        $orderId = $payload->order_id;

        // get transaction data based on order_id
        $transaction = Transaction::where('order_id', $orderId)->first();

        if (!$transaction) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot find transaction with order id: ' . $orderId
            ], 404);
        }

        // Handle transaction status
        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                // Set payment status in merchant's database to 'challenge'
                $transaction->update([
                    'status' => 'challenge'
                ]);
            } elseif ($fraudStatus == 'accept') {
                // Set payment status in merchant's database to 'success'
                $transaction->update([
                    'status' => 'success'
                ]);
            }
        } elseif ($transactionStatus == 'cancel') {
            if ($fraudStatus == 'challenge' || $fraudStatus == 'accept') {
                // Set payment status in merchant's database to 'failed'
                $transaction->update([
                    'status' => 'failed'
                ]);
            }
        } elseif ($transactionStatus == 'deny') {
            // Set payment status in merchant's database to 'failed'
            $transaction->update([
                'status' => 'failed'
            ]);
        }

        // Send a 200 OK response to Midtrans API
        return response()->json([
            'status' => 'OK',
        ]);
    }
}
