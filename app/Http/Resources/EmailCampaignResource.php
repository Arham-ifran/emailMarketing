<?php

namespace App\Http\Resources;

use App\Models\Admin\PackageSubscription;
use App\Models\EmailCampaignLogs;
use App\Models\Group;
use Illuminate\Http\Resources\Json\JsonResource;

class EmailCampaignResource extends JsonResource
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

        // encoding each group_id
        $grp_ids = [];
        if ($this->group_ids)
            foreach (array_column(json_decode($this->group_ids), 'id') as $grp) {
                array_push($grp_ids, \Hashids::encode($grp));
            }

        $sub = PackageSubscription::where('id', $this->subscription_id)->first();

        return [
            //'id' => $this->id,
            'hash_id' => \Hashids::encode($this->id),
            'user_id' => \Hashids::encode($this->name),
            'name' => $this->name,
            'subject' => $this->subject,
            'sender_name' => $this->sender_name,
            'sender_email' => $this->sender_email,
            'reply_to_email' => $this->reply_to_email,
            'track_opens' => $this->track_opens,
            'track_clicks' => $this->track_clicks,
            'status' => $status,
            'size_of_group' => $this->size_of_group,
            'campaign_type' => $this->campaign_type,
            'recursive_campaign_type' => $this->recursive_campaign_type,
            'day_of_week' => $this->day_of_week,
            'day_of_month' => $this->day_of_month,
            'month_of_year' => $this->month_of_year,
            'schedule_date' => date('Y-m-d', strtotime($this->schedule_date)),
            'package_name' => $sub ? $sub->package->title : "",
            'no_of_time' => $this->no_of_time,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'split_test_param' => $this->split_test_param,
            'template_id' => $this->template_id,
            'group_id' => \Hashids::encode($this->group_id),
            'group_ids' => $grp_ids,
            'total_email_sent' => EmailCampaignLogs::where('campaign_id', $this->id)->where('failed_at', NULL)->get()->count(),
            'subscribers' => Group::where('id', $this->group_id)->first() ? Group::where('id', $this->group_id)->with('contacts')->first()->contacts->where('subscribed', 1)->count() : 0,
            'unsubscribers' => Group::where('id', $this->group_id)->with('contacts')->first() ? Group::where('id', $this->group_id)->with('contacts')->first()->contacts->where('subscribed', 0)->count() : 0,
            'contacts' => $this->contacts,
            'excludes' => $this->excludes,
            'initiated_at' => $this->reports->count() ? (CampaignHistoryResource::collection($this->reports)[0]['sent_to']->count() ? CampaignHistoryResource::collection($this->reports)[0]['sent_to'][0]['pivot']['started_at'] : $this->reports[0]->created_at) : $this->created_at,
            'processed_at' => $this->reports->count() ? (sizeof(CampaignHistoryResource::collection($this->reports)[sizeof($this->reports) - 1]['sent_to']) ? (CampaignHistoryResource::collection($this->reports)[sizeof($this->reports) - 1]['sent_to'][sizeof(CampaignHistoryResource::collection($this->reports)[sizeof($this->reports) - 1]['sent_to']) - 1]['pivot']['started_at']) : $this->reports[$this->reports->count() - 1]->created_at) : $this->created_at,
            'split_subject_1' => $this->split_subject_line_1,
            'split_subject_2' => $this->split_subject_line_2,
            'split_content_1' => $this->split_email_content_1,
            'split_content_2' => $this->split_email_content_2,
            'stopped_at' => $this->sending_stopped_at,
        ];
    }
}
