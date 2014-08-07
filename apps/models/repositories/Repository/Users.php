<?php
namespace Models\Repositories\Repository;

use Models\Entities\User as EntityUser;

class Users
{
    public function getLastFive()
    {
        return EntityUser::find(array('limit' => 5))->toArray();
    }
}