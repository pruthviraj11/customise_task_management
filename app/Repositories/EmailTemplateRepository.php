<?php

namespace App\Repositories;

use App\Models\EmailTemplate;

class EmailTemplateRepository
{
    public function find($id)
    {
        return EmailTemplate::find($id);
    }


    public function create(array $data)
    {
        return EmailTemplate::create($data);
    }

    public function update($id, array $data)
    {
        return EmailTemplate::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return EmailTemplate::where('id', $id)->delete();
    }

    public function getAll()
    {
        return EmailTemplate::all();
    }
}
