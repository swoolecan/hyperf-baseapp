<?php
declare(strict_types = 1);

namespace Swoolecan\Baseapp\Resources;

//use Illuminate\Http\Resources\Json\JsonResource;

class DemoResource //extends JsonResource
{
    public function toArray($request)
    {
        return [
            'nickname' => $this->name,
            'email' => $this->email,
        ];
    }
}
