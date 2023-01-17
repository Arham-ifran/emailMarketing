<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageLinkedFeatureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'feature_id' => $this->feature_id,
            'name'       => $this->feature->name,
            'info'       => $this->feature->info,
            'count'      => $this->count
        ];
    }
}
