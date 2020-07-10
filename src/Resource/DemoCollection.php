<?php
declare(strict_types = 1);

namespace Swoolecan\Baseapp\Resource;

//use Illuminate\Http\Resources\Json\ResourceCollection;

class DemoCollection //extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($item) {
            return [
                'nickname' => $item->name,
                'email' => $item->email,
            ];
        })->all();
    }
}
