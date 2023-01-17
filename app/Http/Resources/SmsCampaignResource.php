<?php

namespace App\Http\Resources;

use App\Models\Admin\PackageSubscription;
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
        // encoding each group_id
        $grp_ids = [];
        if ($this->group_ids)
            foreach (array_column(json_decode($this->group_ids), 'id') as $grp) {
                array_push($grp_ids, \Hashids::encode($grp));
            }

        if ($this->status == 1) $status = 'Draft';
        else if ($this->status == 2) $status = 'Sending';
        else if ($this->status == 3) $status = 'Sent';
        else if ($this->status == 4) $status = 'Disabled';
        else if ($this->status == 5) $status = 'Active';
        else if ($this->status == 6) $status = 'Stopped';
        else if ($this->status == 7) $status = 'Processing';

        $sub = PackageSubscription::where('id', $this->subscription_id)->first();

        return [
            //'id' => $this->id,
            'hash_id' => \Hashids::encode($this->id),
            'user_id' => \Hashids::encode($this->user_id),
            'name' => $this->name,
            'message' => $this->message ? $this->message : '',
            'type' => $this->type,
            'recursive_campaign_type' => $this->recursive_campaign_type,
            'package_name' => $sub ? $sub->package->title : "",
            'group_id' => \Hashids::encode($this->group_id),
            'group_ids' => $grp_ids,
            'sender_name' => $this->sender_name,
            'sender_number' => $this->sender_number ? $this->sender_number : '',
            // 1=draft, 2=sending, 3=sent, 4=deleted
            'day_of_week' => $this->day_of_week,
            'day_of_month' => $this->day_of_month,
            'month_of_year' => $this->month_of_year,
            'schedule_date' => date('Y-m-d', strtotime($this->schedule_date)),
            'no_of_time' => $this->no_of_time,
            'status' => $status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'sending_started_at' => $this->sending_started_at,
            'stopped_at' => $this->sending_stopped_at,
            'groups' => $this->groups,
            'recursion' => $this->recursion,
            'schedule' => $this->schedule,
            'reports' => CampaignHistoryResource::collection($this->reports),
            'contacts' => $this->contacts,
            'excludes' => $this->excludes,
            // 'unsubscribers' => $this->sending_to == 1 ? ($this->unsubscribers->contacts ? $this->unsubscribers->contacts->where('subscribed', 0)->count() : 0) : ($this->sending_to == 2 ? $this->unsubscribers->count() : 0),
            'unsubscribers' => $this->sending_to == 1 ? (($this->unsubscribers && $this->unsubscribers[0]) ? $this->unsubscribers[0]->contacts->where('subscribed', 0)->count() : 0) : ($this->sending_to == 2 ? $this->unsubscribers->count() : 0),
            'initiated_at' => $this->reports->count() ? (CampaignHistoryResource::collection($this->reports)[0]['sent_to']->count() ? CampaignHistoryResource::collection($this->reports)[0]['sent_to'][0]['pivot']['started_at'] : $this->reports[0]->created_at) : $this->created_at,
            'processed_at' => $this->reports->count() ? (sizeof(CampaignHistoryResource::collection($this->reports)[sizeof($this->reports) - 1]['sent_to']) ? (CampaignHistoryResource::collection($this->reports)[sizeof($this->reports) - 1]['sent_to'][sizeof(CampaignHistoryResource::collection($this->reports)[sizeof($this->reports) - 1]['sent_to']) - 1]['pivot']['started_at']) : $this->reports[$this->reports->count() - 1]->created_at) : $this->created_at,
        ];
    }
}
