<?php
namespace App\Repositories;

use App\Models\Status;

class StatusRepository
{
    public function find($id)
    {
        return Status::find($id);
    }

    public function create(array $data)
    {
        return Status::create($data);
    }

    public function update($id, array $data)
    {
        return Status::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        Status::Where('id', $id)->update(['deleted_by' => auth()->user()->id]);
        return Status::where('id', $id)->delete();
    }
    public function getAll()
    {
        return Status::where('status', 'on')->get();
    }
}
