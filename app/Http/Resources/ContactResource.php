<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Group;

class ContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // 'subscribed', 'unsubscribed_at', 'confirmed_at'

        return [
            //'id' => $this->id,
            'hash_id' => \Hashids::encode($this->id),
            'user_id' => \Hashids::encode($this->user_id),
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'for_sms' => $this->for_sms,
            'for_email' => $this->for_email,
            // 'country_code' => $this->country_code,
            'number' => $this->number,
            'email' => $this->email,
            'status' => $this->status == 1 ? 'Active' : 'Disabled',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'groups' => $this->groups,
            // 'groups' => GroupResource::collection($this->groups),
            // 'sms_campaigns' => SmsCampaignResource::collection($this->sms_campaigns),
        ];
    }
}
