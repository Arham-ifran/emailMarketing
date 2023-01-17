<?php

namespace App\Http\Resources\RestResources;

use Illuminate\Http\Resources\Json\JsonResource;

class TemplateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return $this;
        return [
            //'id' => $this->id,
            '_id' => \Hashids::encode($this->id),
            'name' => $this->name,
            // 'subject' => $this->subject,
            // 'sender_name' => $this->sender_name,
            // 'sender_email' => $this->sender_email,
            // 'reply_to_email' => $this->reply_to_email,
        ];
    }
}
