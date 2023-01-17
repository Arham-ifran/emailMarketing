<?php

namespace App\Http\Resources\RestResources;

use Illuminate\Http\Resources\Json\JsonResource;

class ECampaignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $status = '';
        if ($this->status == 1) {
            $status = 'Active';
        } elseif ($this->status == 2) {
            $status = 'Draft';
        } elseif ($this->status == 3) {
            $status = 'Disabled';
        } elseif ($this->status == 4) {
            $status = 'Sending';
        } elseif ($this->status == 5) {
            $status = 'Sent';
        } elseif ($this->status == 6) {
            $status = 'Stopped';
        } elseif ($this->status == 7) {
            $status = 'Processing';
        }

        return [
            //'id' => $this->id,
            '_id' => \Hashids::encode($this->id),
            'name' => $this->name,
            'subject' => $this->subject,
            'status' => $status,
            'sender_name' => $this->sender_name,
            'sender_email' => $this->sender_email,
            'reply_to_email' => $this->reply_to_email,
            'initiated_at' => $this->reports->count() ? (CampaignHistoryResource::collection($this->reports)[0]['sent_to']->count() ? CampaignHistoryResource::collection($this->reports)[0]['sent_to'][0]['pivot']['started_at'] : $this->reports[0]->created_at) : $this->created_at,
            'processed_at' => $this->reports->count() ? (sizeof(CampaignHistoryResource::collection($this->reports)[sizeof($this->reports) - 1]['sent_to']) ? (CampaignHistoryResource::collection($this->reports)[sizeof($this->reports) - 1]['sent_to'][sizeof(CampaignHistoryResource::collection($this->reports)[sizeof($this->reports) - 1]['sent_to']) - 1]['pivot']['started_at']) : $this->reports[$this->reports->count() - 1]->created_at) : $this->created_at,
        ];
    }
}
