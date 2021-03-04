<?php

namespace App\Jobs;

use App\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessTransaction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Transaction
     */
    private $transaction;


    /**
     * Create a new job instance.
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = \App\Models\Transaction::where('user_id', $this->transaction->orderNumber)
            ->first();

        if (!$user) {
            \App\Models\Transaction::create([
                'transaction_id' => $this->transaction->transaction_id,
                'user_id' => $this->transaction->orderNumber,
                'sum' => $this->calculateSum($this->transaction->sum, $this->transaction->commissionFee),
            ]);
        } else {
            $user->sum += $this->calculateSum($this->transaction->sum, $this->transaction->commissionFee);
            $user->save();
        }

    }


    /**
     * Calculate sum with commission fee
     * @param int $amount
     * @param float $percent
     * @return float|null
     */
    private function calculateSum(int $amount, float $percent): ?float
    {
        $commissionFee = $amount * ($percent / 100);

        return $amount - $commissionFee;
    }
}
