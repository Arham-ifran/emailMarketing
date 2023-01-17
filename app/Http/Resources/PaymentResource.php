<?php

namespace App\Http\Resources;

use App\Models\Admin\Timezone;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $timezone = Timezone::where('name', $this->user->timezone)->first()->utc_offset;
        $subscription = $this->subscription;
        $payment_method = $this->payment_method;

        // switch ($this->payment_method) {
        //     case config('constants.payment_methods')['PAYPAL']:
        //         $payment_method = 'Paypal';
        //         break;
        //     case config('constants.payment_methods')['MOLLIE']:
        //         $payment_method = 'Mollie';
        //         break;
        //     case config('constants.payment_methods')['ADMIN']:
        //         $payment_method = 'Admin';
        //         break;
        //     case config('constants.payment_methods')['VOUCHER_PROMOTION']:
        //         $payment_method = 'Voucher Promotion';
        //         break;
        // }

        return [
            'id' => $this->id,
            'hash_id' => \Hashids::encode($this->id),
            'item' => $this->item,
            'amount' => $this->amount,
            'vat_percentage' => $this->vat_percentage,
            'vat_amount' => $this->vat_amount,
            'discount_percentage' => $this->discount_percentage,
            'discount_amount' => $this->discount_amount,
            'reseller' => $this->reseller,
            'voucher' => $this->voucher,
            'total_amount' => $this->total_amount,
            'payment_method' => $payment_method,
            'status' => $this->status,
            'payment_date' => \Carbon\Carbon::createFromTimeStamp($this->timestamp, "UTC")->tz($timezone)->format('d M, Y'),
            'end_date' => empty($subscription->end_date) ? 'Lifetime' : \Carbon\Carbon::createFromTimeStamp($subscription->end_date, "UTC")->format('d M, Y'),
            'created_at' => (string) $this->created_at,
        ];
    }
}
