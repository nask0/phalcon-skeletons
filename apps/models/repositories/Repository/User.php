<?php
namespace Models\Repositories\Repository;

use Models\Entities\User as EntityUser;

class User
{
    public function getLast()
    {
        return EntityUser::find()->toArray();
    }
}
