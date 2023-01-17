<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Size;

class SmsCampaignPDFResource extends JsonResource
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
            'message' => $this->message,
            'type' => $this->type,
            'recursive_campaign_type' => $this->recursive_campaign_type,
            // 'send_to_group' => \Hashids::encode($this->send_to_group),
            'group_id' => \Hashids::encode($this->group_id),
            'sender_name' => $this->sender_name,
            'sender_number' => $this->sender_number ? $this->sender_number : '',
            'track_opens' => $this->track_opens,
            'track_clicks' => $this->track_clicks,
            // 1=draft, 2=sending, 3=sent, 4=deleted
            'day_of_week' => $this->day_of_week,
            'day_of_month' => $this->day_of_month,
            'month_of_year' => $this->month_of_year,
            'schedule_date' => $this->schedule_date,
            'no_of_time' => $this->no_of_time,
            'status' => $this->status == 1 ? 'Draft' : ($this->status == 2 ? 'Sending' : ($this->status == 3 ? 'Sent' : 'Disabled')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'sending_started_at' => $this->sending_started_at,
            'stopped_at' => $this->sending_stopped_at,
            'groups' => $this->groups,
            'recursion' => $this->recursion,
            'schedule' => $this->schedule,
            'reports' => CampaignHistoryResource::collection($this->reports),
            'contacts' => $this->contacts,
            'excludes' => $this->excludes->count(),
            // 'unsubscribers' => $this->sending_to == 1 ? ($this->unsubscribers->contacts ? $this->unsubscribers->contacts->where('subscribed', 0)->count() : 0) : ($this->sending_to == 2 ? $this->unsubscribers->count() : 0),
            'unsubscribers' => $this->sending_to == 1 ? (($this->unsubscribers && $this->unsubscribers[0]) ? $this->unsubscribers[0]->contacts->where('subscribed', 0)->count() : 0) : ($this->sending_to == 2 ? $this->unsubscribers->count() : 0),
            'initiated_at' => $this->reports->count() ? (CampaignHistoryResource::collection($this->reports)[0]['sent_to']->count() ? CampaignHistoryResource::collection($this->reports)[0]['sent_to'][0]['pivot']['started_at'] : $this->reports[0]->created_at) : $this->created_at,
            'processed_at' => $this->reports->count() ? (sizeof(CampaignHistoryResource::collection($this->reports)[sizeof($this->reports) - 1]['sent_to']) ? (CampaignHistoryResource::collection($this->reports)[sizeof($this->reports) - 1]['sent_to'][sizeof(CampaignHistoryResource::collection($this->reports)[sizeof($this->reports) - 1]['sent_to']) - 1]['pivot']['started_at']) : $this->reports[$this->reports->count() - 1]->created_at) : $this->created_at,
        ];
    }
}
