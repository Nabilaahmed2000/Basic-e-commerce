<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => (float) $this->price,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'images' => $this->getMediaUrls('photos'),
        ];
    }
    protected function getMediaUrls(string $collectionName): array
    {
        return $this->getMedia($collectionName)->map(function ($item) {
            return $item->getUrl();
        })->toArray();
    }
}
