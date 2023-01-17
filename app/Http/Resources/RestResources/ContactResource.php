<?php

namespace App\Http\Resources\RestResources;

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
            '_id' => \Hashids::encode($this->id),
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'number' => $this->number ? $this->number : "",
            'email' => $this->email ? $this->email : "",
        ];
    }
}
