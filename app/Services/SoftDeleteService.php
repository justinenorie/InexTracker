<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class SoftDeleteService
{
    /**
     * List trashed records for a user.
     *
     * Assumes the model has a `user_id` column and uses SoftDeletes.
     *
     * @template TModel of Model
     *
     * @param  class-string<TModel>  $modelClass
     * @param  array<int, string>  $with
     * @param  (callable(Builder<TModel>): void)|null  $tap
     * @return Collection<int, TModel>
     */
    public function listTrashedForUser(string $modelClass, User $user, array $with = [], ?callable $tap = null): Collection
    {
        /** @var Builder<TModel> $query */
        $query = $modelClass::onlyTrashed()->where('user_id', $user->id);

        if ($with !== []) {
            $query->with($with);
        }

        if ($tap) {
            $tap($query);
        }

        return $query->orderByDesc('deleted_at')->get();
    }

    /**
     * Restore a trashed record by id for a user.
     *
     * @template TModel of Model
     *
     * @param  class-string<TModel>  $modelClass
     */
    public function restoreForUser(string $modelClass, string $id, User $user): bool
    {
        /** @var TModel $model */
        $model = $modelClass::onlyTrashed()
            ->where('user_id', $user->id)
            ->findOrFail($id);

        return (bool) $model->restore();
    }

    /**
     * Permanently delete a trashed record by id for a user.
     *
     * @template TModel of Model
     *
     * @param  class-string<TModel>  $modelClass
     */
    public function forceDeleteForUser(string $modelClass, string $id, User $user): bool
    {
        /** @var TModel $model */
        $model = $modelClass::onlyTrashed()
            ->where('user_id', $user->id)
            ->findOrFail($id);

        return (bool) $model->forceDelete();
    }
}
