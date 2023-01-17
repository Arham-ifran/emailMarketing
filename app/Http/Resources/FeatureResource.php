<?php

namespace App\Http\Resources;

use App\Models\LanguageTranslation;
use Illuminate\Http\Resources\Json\JsonResource;

class FeatureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (!$request->lang) {
            $request['lang'] = 'en';
        }
        // $name = translation($this->id, 8, $request->lang, 'name', $this->name);
        // $description = translation($this->id, 8, $request->lang, 'description', $this->description);
        $description = $this->description;
        $name = $this->name;

        $search = [];
        $replace = [];
        $ids = [];
        $labels = $this->FeatureLabels;

        foreach ($labels as $object) {
            $search[$object->id] = '{{' . $object->label . '}}';
            $replace[$object->id] = $object->value;
            $ids[] = $object->id;
        }

        if ($request->lang != 'en') {
            $translations = LanguageTranslation::where(['language_module_id' => 15, 'language_code' => $request->lang, 'column_name' => 'value'])->whereIn('item_id', $ids)->get();

            foreach ($translations as $translation) {
                $replace[$translation->item_id] = $translation->item_value;
            }
        }

        $description  = str_replace($search, $replace, $description);
        $name  = str_replace($search, $replace, $name);

        return [
            'id'      => $this->id,
            'image'      => $this->image,
            'image_position' => $this->image_position,
            'name'    => $name,
            'description' => $description,
            'lang'    => $request->lang
        ];
    }
}
