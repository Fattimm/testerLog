<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    /**
     * Crée un nouvel utilisateur.
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Récupère tous les utilisateurs.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return User::all();
    }

    /**
     * Trouve un utilisateur par ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Supprime un utilisateur.
     *
     * @param User $user
     * @return bool|null
     */
    public function delete(User $user): ?bool
    {
        return $user->delete();
    }
}
