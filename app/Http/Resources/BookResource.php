<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
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
            // @TODO implement
              'id' => (int)$this->id,
              'isbn' => $this->isbn,
              'title' => $this->title,
              'description' => $this->description,
              'published_year'=> $this->published_year,
              'authors' => $this->authors,
              'review' => [
                'avg' => round($this->avg_review),
                'count' => (int)$this->count
              ]
        ];
    }
}
