<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmailListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this['id'],
            'uuid' => $this['uuid'],
            'name' => $this->when(!is_null($this['name']), $this['name']),

            'free_mails_stat' => $this->when(!is_null($this['free_mails_stat']), $this['free_mails_stat']),
            'top_domains' => $this->when(!is_null($this['top_domains']), $this['top_domains']),

            'error' => $this->when(!is_null($this['error']), $this['error']),
            'created_at' => $this->when(!is_null($this['created_at']), $this['created_at']->format('jS F, Y H:i:s')),
            'updated_at' => $this->when(!is_null($this['updated_at']), $this['updated_at']->format('jS F, Y H:i:s')),
        ];
    }
}
