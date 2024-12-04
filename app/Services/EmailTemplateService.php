<?php
namespace App\Services;

use App\Repositories\EmailTemplateRepository;

class EmailTemplateService

{
    protected EmailTemplateRepository $emailTemplateRepository;

    public function __construct(EmailTemplateRepository $emailTemplateRepository)
    {
        $this->emailTemplateRepository = $emailTemplateRepository;;
    }

    public function create($emailTemplateData)
    {
        $emailTemplate = $this->emailTemplateRepository->create($emailTemplateData);
        return $emailTemplate;
    }

    public function getAllEmailTemplate()
    {
        $emailTemplate = $this->emailTemplateRepository->getAll();
        return $emailTemplate;
    }

    public function getEmailTemplate($id)
    {
        $emailTemplate = $this->emailTemplateRepository->find($id);
        return $emailTemplate;
    }

    public function deleteEmailTemplate($id)
    {
        $deleted = $this->emailTemplateRepository->delete($id);
        return $deleted;
    }

    public function updateEmailTemplate($id, $emailTemplateData)
    {
        $updated = $this->emailTemplateRepository->update($id, $emailTemplateData);
        return $updated;
    }

}

?>
