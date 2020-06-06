<?php

declare(strict_types = 1);

namespace Swoolecan\Baseapp\Repositories;

use Doctrine\Common\Collections\Collection;
use MyBlog\User;

class AbstractRepository
{
    /** 
     * @var AbstractModel
     */
    protected $model;

    /**
     * UserRepository constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
