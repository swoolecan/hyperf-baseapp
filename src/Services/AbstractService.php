<?php

declare(strict_types = 1);

namespace Swoolecan\Baseapp\Services;

use Doctrine\Common\Collections\Collection;

class AbstractService
{
    /** 
     * @var AbstractRepository
     */
    protected $repository;

    /**
     * UserService constructor.
     * @param User $user
     */
    /*public function __construct(User $user)
    {
        $this->user = $user;
    }*/
}
