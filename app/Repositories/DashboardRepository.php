<?php
namespace App\Repositories;

use App\Models\Dashboard;

class DashboardRepository
{
    public function find($id)
    {
        return Dashboard::find($id);
    }

    public function create(array $data)
    {
        return Dashboard::create($data);
    }

    public function update($id, array $data)
    {
        return Dashboard::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return Dashboard::where('id', $id)->delete();
    }
    public function getAll()
    {
        return Dashboard::all();
    }
}
