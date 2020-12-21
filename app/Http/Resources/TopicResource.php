<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TopicResource extends JsonResource
{
    protected $hiddenSensitiveFields = true;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (!$this->hiddenSensitiveFields) {
            $this->resource->addHidden(['body']);
        }
        $data = parent::toArray($request);
        $data['user'] = new UserResource($this->whenLoaded('user'));
        $data['category'] = new CategoriesResource($this->whenLoaded('category'));

        return $data;
    }

    public function hiddenSensitiveFields()
    {
        $this->hiddenSensitiveFields = false;

        return $this;
    }
}
