<?php

namespace App\Http\Resources\RestResources;

use Illuminate\Http\Resources\Json\JsonResource;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Size;

class SmsCampaignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->status == 1) $status = 'Draft';
        else if ($this->status == 2) $status = 'Sending';
        else if ($this->status == 3) $status = 'Sent';
        else if ($this->status == 4) $status = 'Disabled';
        else if ($this->status == 5) $status = 'Active';
        else if ($this->status == 6) $status = 'Stopped';
        else if ($this->status == 7) $status = 'Processing';

        return [
            //'id' => $this->id,
            '_id' => \Hashids::encode($this->id),
            'name' => $this->name,
            'message' => $this->message,
            'sender_name' => $this->sender_name,
            'reply_to_number' => $this->sender_number ? $this->sender_number : '',
            'status' => $status,
            // 'sending_started_at' => $this->sending_started_at,
            // 'stopped_at' => $this->sending_stopped_at,
            'initiated_at' => $this->reports->count() ? (CampaignHistoryResource::collection($this->reports)[0]['sent_to']->count() ? CampaignHistoryResource::collection($this->reports)[0]['sent_to'][0]['pivot']['started_at'] : $this->reports[0]->created_at) : $this->created_at,
            'processed_at' => $this->reports->count() ? (sizeof(CampaignHistoryResource::collection($this->reports)[sizeof($this->reports) - 1]['sent_to']) ? (CampaignHistoryResource::collection($this->reports)[sizeof($this->reports) - 1]['sent_to'][sizeof(CampaignHistoryResource::collection($this->reports)[sizeof($this->reports) - 1]['sent_to']) - 1]['pivot']['started_at']) : $this->reports[$this->reports->count() - 1]->created_at) : $this->created_at,
        ];
    }
}
