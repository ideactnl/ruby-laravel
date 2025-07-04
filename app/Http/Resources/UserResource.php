<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'registration_number' => $this->registration_number,
            'enable_data_sharing' => $this->enable_data_sharing,
            'opt_in_for_research' => $this->opt_in_for_research,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
