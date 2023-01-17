<?php

namespace App\Http\Resources;

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
        $completed = $this->updated_at;
        $started = $this->created_at;
        return [
            'id' => $this->id,
            // 'type' => $this->type,
            'hash_id' => \Hashids::encode($this->id),
            'user_id' => \Hashids::encode($this->user_id),
            'campaign_id' => \Hashids::encode($this->campaign_id),
            'type' => $this->type == 1 ? 'SMS' : ($this->type == 2 ? 'Email' : 'Disabled'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'success' => $this->type == 1 ? $this->sms_success->count() : $this->success->count(),
            'fail' => $this->type == 1 ? $this->sms_fail->count() : $this->fail->count(),
            'bounces' => $this->bounces->count(),
            'sent_to_success' => $this->type == 1 ? $this->sms_success : $this->success,
            'sent_to_fail' => $this->type == 1 ? $this->sms_fail : $this->fail,
            'sent_to_bounces' => $this->bounces,
            'sent_to' => $this->type == 1 ? $this->sms_sent_to : $this->sent_to,
            'started_at' => $started ? $started : $this->created_at,
            'completed_at' =>  $completed ? $completed : $this->created_at,
        ];
    }
}
