<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\CmsPageLabel;
use App\Models\LanguageTranslation;

class CmsPageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $title = translation($this->id, 5, $request->lang, 'title', $this->title);
        $content = $this->content;

        $search = [];
        $replace = [];
        $ids = [];
        $labels = $this->cmsPageLabels;

        foreach ($labels as $object) {
            $search[$object->id] = '{{' . $object->label . '}}';
            $replace[$object->id] = $object->value;
            $ids[] = $object->id;
        }

        if ($request->lang != 'en') {
            $translations = LanguageTranslation::where(['language_module_id' => 9, 'language_code' => $request->lang, 'column_name' => 'value'])->whereIn('item_id', $ids)->get();

            foreach ($translations as $translation) {
                $replace[$translation->item_id] = $translation->item_value;
            }
        }

        $content  = str_replace($search, $replace, $content);

        return [
            'id'      => $this->id,
            'title'   => $title,
            'slug'    => $this->slug,
            'content' => $content,
            'lang'    => $request->lang
        ];
    }
}
