<?php


namespace App\Http\Controllers\Api\v1;


use App\Jobs\ProcessTransaction;
use App\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    /**
     * Handle incoming transaction
     * @param Request $request
     * @return JsonResponse
     */
    public function retrieve(Request $request): JsonResponse
    {
        $hash = $this->calculateHash($request->get('transaction'));

        $transaction = (new Transaction())
            ->setSum($request->transaction['sum'])
            ->setCommissionFee($request->transaction['commission_fee'])
            ->setOrderNumber($request->transaction['order_id'])
            ->setTransactionId($request->transaction['transaction_id']);

        if (!hash_equals($hash, $request->hash)) {

            return response()->json([
                'status' => 'error',
                'message' => 'invalid hash'
            ]);
        }

        $this->dispatch((new ProcessTransaction($transaction))
            ->onQueue('transactions'));

        return response()->json([
            'status' => 'success',
            'message' => 'Transaction put into queue',
        ]);
    }

    /**
     * Validate incoming hash
     * @param array $data
     * @return string
     */
    private function calculateHash(array $data): string
    {
        $hash = '';

        foreach ($data as $key => $item) {

            $hash .= $key . $item;
        }

        return md5($hash);
    }
}
