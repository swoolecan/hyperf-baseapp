<?php
declare(strict_types = 1);

namespace Swoolecan\Baseapp\Resources;

use Fangx\Resource\Json\JsonResource;

class AbstractResource extends JsonResource
{
    protected $_scene;

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
        ];
    }

    public function setScene($scene)
    {
        $this->_scene = $scene;
    }

    public function getScene()
    {
        return $this->_scene;
    }
}
