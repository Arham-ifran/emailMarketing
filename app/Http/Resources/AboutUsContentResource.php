<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Admin\AboutUsContentLabel;
use App\Models\LanguageTranslation;

class AboutUsContentResource extends JsonResource
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
        $labels = $this->aboutUsContentLabels;

        foreach ($labels as $object) {
            $search[$object->id] = '{{' . $object->label . '}}';
            $replace[$object->id] = $object->value;
            $ids[] = $object->id;
        }

        if ($request->lang != 'en') {
            $translations = LanguageTranslation::where(['language_module_id' => 13, 'language_code' => $request->lang, 'column_name' => 'value'])->whereIn('item_id', $ids)->get();

            foreach ($translations as $translation) {
                $replace[$translation->item_id] = $translation->item_value;
            }
        }

        $description  = str_replace($search, $replace, $description);
        $name  = str_replace($search, $replace, $name);

        return [
            'id'      => $this->id,
            'image'      => $this->image,
            'name'    => $name,
            'description' => $description,
            'image_path'   => checkImage(asset('storage/home-contents/' . $this->image), 'placeholder.png'),
            'image_position' => $this->image_position,
            'lang'    => $request->lang
        ];
    }
}
