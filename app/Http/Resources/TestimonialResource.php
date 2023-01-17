<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $message = $this->message;

        if ($request->lang != 'en') {
            $message = translationByDeepL($this->message, $request->lang);
            // $question = $this->question;
            // $answer = $this->answer;
        }

        return [
            'id'         => $this->id,
            'message'   => $message,
            'name'   => $this->name,
            'lang'       => $request->lang
        ];
    }
}
