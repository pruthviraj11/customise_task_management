<?php
namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function find($id)
    {
        return User::find($id);
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function update($id, array $data)
    {
        $user = User::findOrFail($id);
        return $user->update($data);
    }

    public function delete($id)
    {
        User::Where('id', $id)->update(['deleted_by' => auth()->user()->id]);
        return User::findOrFail($id)->delete();

    }
    public function getAll()
    {
        return User::query();
    }
}
