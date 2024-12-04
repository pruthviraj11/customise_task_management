<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailTemplate\CreateEmailTemplateRequest;
use App\Services\EmailTemplateService;
use DataTables;
use App\Http\Requests\EmailTemplate\UpdateEmailTemplateRequest;


class EmailTemplateController extends Controller
{
    protected EmailTemplateService $emailTemplateService;

    public function __construct(EmailTemplateService $emailTemplateService)
    {
        $this->emailTemplateService = $emailTemplateService;
    }

    public function index()
    {
        return view('content/apps/email-templates/list');
    }

    public function create()
    {
        $email_template = "";
        $page_data['page_title'] = "Add New Email Template";
        $page_data['form_title'] = "New Email Template";
        return view('content/apps/email-templates/create-edit', compact('page_data', 'email_template'));

    }

    public function getall()
    {
        $discount = $this->emailTemplateService->getAllEmailTemplate();
        return DataTables::of($discount)
            ->addColumn('status', function ($row) {
                if ($row->status == true) {
                    $status = "<span class='badge badge-light-success'>Active</span>";
                } else {
                    $status = "<span class='badge badge-light-warning'>Inactive</span>";
                }
                return $status;
            })
            ->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->id);
                // Update Button
                $updateButton = "<a class='btn btn-warning'  href='" . route('app-email-templates-edit', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";

                // Delete Button
                $deleteButton = "<a class='btn btn-danger confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-email-templates-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

                return $updateButton . " " . $deleteButton;
            })->rawColumns(['actions', 'status'])->make(true);
    }

    public function store(CreateEmailTemplateRequest $request)
    {
        try {
            //            dd($request->all());
            $emailTemplate['title'] = $request->get('title');
            $emailTemplate['subject'] = $request->get('subject');
            $emailTemplate['description'] = $request->get('description');
            $emailTemplate['html'] = $request->get('html');
            $emailTemplate['status'] = $request->get('status') ? true : false;
            //dd($emailTemplate);
            $emailTemplate = $this->emailTemplateService->create($emailTemplate);

            if (!empty($emailTemplate)) {
                //                return $emailTemplate;
//                return response()->json(['status'=>true, 'message'=>'Email Template Added Successfully']);
                return redirect()->route('app-email-templates-list')->with('success', 'Email Template Added Successfully');
            } else {
                //                return response()->json(['status'=>true, 'message'=>'Error while Adding Email Template']);
                return redirect()->back()->with('error', 'Error while Adding Email Template');
            }
        } catch (\Exception $error) {
            dd($error->getMessage());
            //            return response()->json(['status'=>true, 'message'=>$error->getMessage()]);
            return redirect()->route('app-email-templates-list')->with('error', 'Error while adding Email Template');
        }

    }

    public function edit($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $email_template = $this->emailTemplateService->getEmailTemplate($id);
            $page_data['page_title'] = "Edit Email Template";
            $page_data['form_title'] = "Edit Email Template";
            return view('content/apps/email-templates/create-edit', compact('page_data', 'email_template'));
        } catch (\Exception $error) {
            return redirect()->route("app-email-templates-list")->with('error', 'Error while editing Email Template');
        }
    }

    public function update(UpdateEmailTemplateRequest $request, $encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $emailTemplate['title'] = $request->get('title');
            $emailTemplate['subject'] = $request->get('subject');
            $emailTemplate['description'] = $request->get('description');
            $emailTemplate['html'] = $request->get('html');
            $emailTemplate['status'] = $request->get('status') ? true : false;
            $updated = $this->emailTemplateService->updateEmailTemplate($id, $emailTemplate);
            if (!empty($updated)) {
                return redirect()->route("app-email-templates-list")->with('success', 'Email Template Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Updating Email Template');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-email-templates-list")->with('error', 'Error while editing Email Template');
        }
    }

    public function destroy($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $deleted = $this->emailTemplateService->deleteEmailTemplate($id);
            if (!empty($deleted)) {
                return redirect()->route("app-email-templates-list")->with('success', 'Email Template Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Deleting Email Template');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-email-templates-list")->with('error', 'Error while editing Email Template');
        }
    }
}
