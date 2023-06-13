<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use App\Models\Transaction;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\API\TransactionRequest;

class TransactionController extends Controller
{
    // Create
    public function create(TransactionRequest $request)
    {
        $product = Product::findOrFail($request->product_id);

        if ($product->quantity < $request->quantity) {
            return ResponseFormatter::error(
                'Not enough product quantity'
            );
        }

        $vat = ($product->price * (10 / 100)) * $request->quantity;
        $total = ($product->price * $request->quantity) + $vat;
        $transaction = Transaction::create([
            'order_id' => 'TRX' . time() . mt_rand(100, 999),
            'user_id' => auth()->id(),
            'product_id' => $request->product_id,
            'price_per_item' => $product->price,
            'quantity' => $request->quantity,
            'vat' => $vat,
            'total' => $total,
            'payment_method' => $request->payment_method,
        ]);

        $product->decrement('quantity', $request->quantity);

        // If payment_method is Gopay, then create a transaction in Midtrans
        if ($request->payment_method === 'gopay') {

            $url = config('services.midtrans.isProduction') ?
                'https://api.midtrans.com/v2/charge' :
                'https://api.sandbox.midtrans.com/v2/charge';

            $serverKey = config('services.midtrans.serverKey');
            $backendUrl = route('midtrans.notification');

            $requestData = [
                'payment_type' => 'gopay',
                'transaction_details' => [
                    'order_id' => $transaction->order_id,
                    'gross_amount' => $transaction->total,
                ]
            ];

            // Encode the server key in base64
            $authHeader = 'Basic ' . base64_encode($serverKey);

            // Send a POST request to the Midtrans API
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => $authHeader,
                'X-Override-Notification' => $backendUrl,
            ])->post($url, $requestData);

            // Get the API response as a JSON object
            $result = $response->json();

            // If status_code 401, return error
            if ($result['status_code'] === "401") {
                return ResponseFormatter::error(
                    'Midtrans server key is invalid',
                    $result
                );
            }

            if (isset($result['actions'][0]['url'])) {
                $transaction->update([
                    'payment_url' => $result['actions'][0]['url'],
                ]);
            }
        }

        return ResponseFormatter::success($transaction, 'Transaction created');
    }
}
