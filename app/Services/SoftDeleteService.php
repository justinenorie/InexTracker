<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class SoftDeleteService
{
    /**
     * List all trashed records for a user.
     *
     * @param  string  $modelClass the model class name
     * @param  User  $user the user instance
     * @param  array  $with the relationships to eager load
     * @return Collection
     */
    public function listTrashedForUser(string $modelClass, User $user, array $with = []): Collection
    {
        /** @var Model $query */
        $query = new $modelClass();

        return $query::onlyTrashed()
            ->where('user_id', $user->id)
            ->with($with)
            ->orderByDesc('deleted_at')
            ->get();
    }

    /**
     * Restore a trashed record for a user.
     *
     * @param  string  $modelClass the model class name
     * @param  string  $id the record unique id
     * @param  User  $user the user instance
     * @return bool
     */
    public function restoreForUser(string $modelClass, string $id, User $user): bool
    {
        /** @var Model $query */
        $query = new $modelClass();

        $record = $query::onlyTrashed()
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if ($record) {
            return $record->restore();
        }

        return false;
    }

    /**
     * Permanently delete a trashed record for a user.
     *
     * @param  string  $modelClass the model class name
     * @param  string  $id the record unique id
     * @param  User  $user the user instance
     * @return bool
     */
    public function forceDeleteForUser(string $modelClass, string $id, User $user): bool
    {
        /** @var Model $query */
        $query = new $modelClass();

        $record = $query::onlyTrashed()
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if ($record) {
            return $record->forceDelete();
        }

        return false;
    }
}
