<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return [
            //'id' => $this->id,
            'hash_id' => \Hashids::encode($this->id),
            'user_id' => \Hashids::encode($this->user_id),
            'name' => $this->name,
            'for_sms' => $this->for_sms,
            'for_email' => $this->for_email,
            'sender_name' => $this->sender_name,
            'sender_email' => $this->sender_email,
            'double_opt_in' => $this->double_opt_in,
            'description' => $this->description,
            'status' => $this->status == 1 ? 'Active' : 'Disabled',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'contacts' => ContactResource::collection($this->contacts),
            // 'sms_campaigns' => SmsCampaignResource::collection($this->sms_campaigns),

        ];
    }
}
