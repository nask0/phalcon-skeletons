<?php
namespace PhalconSkeletons\Models\Repositories\Repository;

use PhalconSkeletons\Models\Entities\User as EntityUser;

class User
{
    public function getLast()
    {
        return EntityUser::find()->toArray();
    }
}
