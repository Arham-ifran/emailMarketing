<?php

namespace App\Http\Resources\RestResources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmailCampaignTemplateResource extends JsonResource
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
            'name' => $this->name,
        ];
    }
}
