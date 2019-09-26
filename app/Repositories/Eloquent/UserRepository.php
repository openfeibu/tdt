<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Eloquent\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * @var array
     */

    public function boot()
    {
        $this->fieldSearchable = config('model.user.user.model.search');
    }

    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return config("auth.providers.users.model");
    }

    /**
     * Find a user by its id.
     *
     * @param type $id
     *
     * @return type
     */
    public function findUser($id)
    {
        return $this->model->whereId($id)->first();
    }


}
