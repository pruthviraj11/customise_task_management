<?php
namespace App\Repositories;

use App\Models\Location;

class LocationRepository
{
    public function find($id)
    {
        return Location::find($id);
    }

    public function create(array $data)
    {
        return Location::create($data);
    }

    public function update($id, array $data)
    {
        return Location::findOrFail( $id)->update($data);
    }

    public function delete($id)
    {
        $data = Location::where('id', $id)->first();


        return $data->delete();
    }
    public function getAll()
    {
        return Location::query();
    }
}
