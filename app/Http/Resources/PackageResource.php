<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $title = $this->title;
        $sub_title = $this->sub_title;
        $description = $this->description;

        if ($request->lang != 'en') {
            $title = translation($this->id, 2, $request->lang, 'title', $this->title);
            $sub_title = translation($this->id, 2, $request->lang, 'sub_title', $this->sub_title);
            $description = translation($this->id, 2, $request->lang, 'description', $this->description);
            // $sub_title = $this->sub_title;
            // $description = $this->description;
        }

        return [
            'id'            => $this->id,
            'title'         => $title,
            'sub_title'     => $sub_title,
            'monthly_price' => $this->monthly_price,
            'yearly_price'  => $this->yearly_price,
            'description'   => $description,
            'features'      => $this->linkedFeatures->pluck('count', 'feature_id'),
            'status'        => $this->status,
            'lang'          => $request->lang,
            'recommended'   => $this->recommended
        ];
    }
}
