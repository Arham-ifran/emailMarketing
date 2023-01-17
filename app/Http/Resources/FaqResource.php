<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $question = $this->question;
        $answer = $this->answer;

        if ($request->lang != 'en') {
            $question = translation($this->id, 3, $request->lang, 'question', $this->question);
            $answer = translation($this->id, 3, $request->lang, 'answer', $this->answer);
            // $question = $this->question;
            // $answer = $this->answer;
        }

        return [
            'id'         => $this->id,
            'question'   => $question,
            'answer'     => $answer,
            'lang'       => $request->lang
        ];
    }
}
