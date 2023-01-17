<?php

namespace App\Http\Resources\RestResources;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignHistoryResource extends JsonResource
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
            '_id' => \Hashids::encode($this->id),
            'campaign_id' => \Hashids::encode($this->campaign_id),
            'success' => $this->type == 1 ? $this->sms_success->count() : $this->success->count(),
            'fail' => $this->type == 1 ? $this->sms_fail->count() : $this->fail->count(),
            'sent_to' => ContactResource::collection($this->type == 1 ? $this->sms_sent_to : $this->sent_to),
            'started_at' => $this->created_at,
            'completed_at' => $this->updated_at,
        ];
    }
}
