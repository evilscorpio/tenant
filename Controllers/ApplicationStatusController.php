<?php

namespace App\Modules\Tenant\Controllers;

use App\Modules\Tenant\Models\Intake\Intake;
use Illuminate\Http\Request;
use App\Modules\Tenant\Models\Application\CourseApplication;
use App\Modules\Tenant\Models\Application\ApplicationStatus;
use App\Modules\Tenant\Models\Notes;
use App\Modules\Tenant\Models\Document;
use Session;
use Flash;

class ApplicationStatusController extends BaseController
{
    function __construct(CourseApplication $application, Request $request, Notes $note, ApplicationStatus $application_status, Document $document, Intake $intake)
    {
        $this->application = $application;
        $this->application_status = $application_status;
        $this->note = $note;
        $this->document = $document;
        $this->request = $request;
        $this->intake = $intake;
        parent::__construct();
    }

    //Information for Enquiry page
    public function index()
    {
        $applications = $this->application_status->getApplications(1);
        return view('Tenant::ApplicationStatus/enquiry', ['applications' => $applications]);
    }

    public function apply_offer($course_application_id)
    {
        $data['application'] = $this->application->getDetails($course_application_id);
        $data['client_name'] = $this->application->getClientName($course_application_id);
        $data['intakes'] = $this->intake->getIntakes($data['application']->institute_id);

        return view('Tenant::ApplicationStatus/action/apply_offer', $data);

    }

    //updates for apply_offer
    public function update($course_application_id)
    {
        $this->application_status->apply_offer($this->request->all(), $course_application_id);
        Flash::success('Offer Applied Successfully.');
        return redirect()->route('applications.offer_letter_processing.index');
    }


    //Information for cancel/quarantine action page whose parent page is Enquiry
    public function cancel_application($course_application_id)
    {
        $applications = CourseApplication::leftjoin('users', 'users.user_id', '=', 'course_application.user_id')
            ->leftjoin('persons', 'persons.person_id', '=', 'users.person_id')
            ->leftjoin('institute_courses', 'institute_courses.institute_course_id', '=', 'course_application.institution_course_id')
            ->leftjoin('courses', 'courses.course_id', '=', 'institute_courses.course_id')
            ->leftjoin('institutes', 'institutes.institution_id', '=', 'institute_courses.institute_id')
            ->leftjoin('companies', 'companies.company_id', '=', 'institutes.company_id')
            ->leftjoin('intakes', 'intakes.intake_id', '=', 'course_application.intake_id')
            ->leftjoin('application_notes', 'course_application.course_application_id', '=', 'application_notes.application_id')
            ->leftjoin('notes', 'application_notes.note_id', '=', 'notes.notes_id')
            ->where('course_application.course_application_id', $course_application_id)
            ->select(['persons.first_name', 'companies.name as company', 'courses.name', 'intakes.intake_date', 'course_application.tuition_fee', 'course_application.course_application_id'])
            ->orderBy('course_application.course_application_id', 'desc')
            ->find($course_application_id);

        return view('Tenant::ApplicationStatus/action/cancel_application', ['applications' => $applications]);
    }

    //cancel/qurantine actions
    public function cancel_qurantine()
    {
        $created = $this->note->note_create($this->request->all());
        if ($created)

            Session::flash('success', 'Quarantinded Successfully');
        return redirect()->route('applications.offer_letter_processing.index');
    }

    //Information for offer letter processing page
    public function offerLetterProcessing()
    {
        $applications = $this->application_status->getApplications(2);
        return view('Tenant::ApplicationStatus/offer_letter_processing', compact('applications'));
    }

    //Information for offer_received action page whose parent page is Offer Letter Processing
    public function offer_letter_received($course_application_id)
    {
        $data['application'] = $this->application->getDetails($course_application_id);
        $data['client_name'] = $this->application->getClientName($course_application_id);
        $data['intakes'] = $this->intake->getIntakes($data['application']->institute_id);

        return view('Tenant::ApplicationStatus/action/offer_letter_received', $data);
    }


    //updates for offer_received
    public function offer_received_update($course_application_id)
    {
        $updated = $this->application_status->offer_received($this->request->all(), $course_application_id);

        Session::flash('success', 'Updated Successfully');
        return redirect()->route('applications.offer_letter_issued.index');
    }

    //information for offer letter issued
    public function offerLetterIssued()
    {
        $applications = $this->application_status->getApplications(3);
        return view('Tenant::ApplicationStatus/offer_letter_issued', compact('applications'));
    }

    public function apply_coe($course_application_id)
    {
        $applications = CourseApplication::leftjoin('users', 'users.user_id', '=', 'course_application.user_id')
            ->leftjoin('persons', 'persons.person_id', '=', 'users.person_id')
            ->leftjoin('institute_courses', 'institute_courses.institute_course_id', '=', 'course_application.institution_course_id')
            ->leftjoin('courses', 'courses.course_id', '=', 'institute_courses.course_id')
            ->leftjoin('institutes', 'institutes.institution_id', '=', 'institute_courses.institute_id')
            ->leftjoin('companies', 'companies.company_id', '=', 'institutes.company_id')
            ->leftjoin('intakes', 'intakes.intake_id', '=', 'course_application.intake_id')
            ->leftjoin('application_notes', 'course_application.course_application_id', '=', 'application_notes.application_id')
            ->leftjoin('notes', 'application_notes.note_id', '=', 'notes.notes_id')
            ->leftjoin('application_status', 'application_status.course_application_id', '=', 'course_application.course_application_id')
            ->leftjoin('application_status_documents', 'application_status_documents.application_status_id', '=', 'application_status.application_status_id')
            ->leftjoin('documents', 'documents.document_id', '=', 'application_status_documents.document_id')
            ->where('course_application.course_application_id', $course_application_id)
            ->select(['intakes.intake_date', 'course_application.tuition_fee', 'course_application.student_id', 'course_application.course_application_id'])
            ->orderBy('course_application.course_application_id', 'desc')
            ->find($course_application_id);

        return view('Tenant::ApplicationStatus/action/apply_coe', compact('applications'));
    }

    //updates for applied_offer
    public function update_applied_coe($course_application_id)
    {
        $updated = $this->application_status->coe_update($this->request->all(), $course_application_id);

        Flash::success('Status Updated Successfully.');
        return redirect()->route('applications.coe_processing.index');
    }

    function uploadDocument($application_id)
    {
        $folder = 'document';
        $file = $this->request->input('document');
        $file = ($file == '') ? 'document' : $file;

        if ($file_info = tenant()->folder($folder, true)->upload($file)) {
            $document_id = $this->document->uploadDocument($application_id, $file_info, $this->request->all());
            $document = Document::find($document_id);
            $this->client->addLog($client_id, 3, ['{{NAME}}' => get_tenant_name(), '{{DESCRIPTION}}' => $document->description, '{{TYPE}}' => $document->type, '{{FILE_NAME}}' => $document->name, '{{VIEW_LINK}}' => $document->shelf_location, '{{DOWNLOAD_LINK}}' => route('tenant.client.document.download', $document_id)]);
            \Flash::success('File uploaded successfully!');
            return redirect()->route('tenant.client.document', $client_id);
        }
    }

    //Information for coe processing page
    public function coeProcessing()
    {
        $applications = $this->application_status->getApplications(4);
        return view('Tenant::ApplicationStatus/coe_processing', compact('applications'));
    }

    //Information for action of coe processing page
    public function action_coe_issued($course_application_id)
    {
        $applications = CourseApplication::leftjoin('users', 'users.user_id', '=', 'course_application.user_id')
            ->leftjoin('persons', 'persons.person_id', '=', 'users.person_id')
            ->leftjoin('person_phones', 'persons.person_id', '=', 'person_phones.person_id')
            ->leftjoin('phones', 'person_phones.phone_id', '=', 'phones.phone_id')
            ->leftjoin('institute_courses', 'institute_courses.institute_course_id', '=', 'course_application.institution_course_id')
            ->leftjoin('courses', 'courses.course_id', '=', 'institute_courses.course_id')
            ->leftjoin('institutes', 'institutes.institution_id', '=', 'institute_courses.institute_id')
            ->leftjoin('companies', 'companies.company_id', '=', 'institutes.company_id')
            ->leftjoin('intakes', 'intakes.intake_id', '=', 'course_application.intake_id')
            ->join('application_status', 'application_status.course_application_id', '=', 'course_application.course_application_id')
            ->join('status', 'status.status_id', '=', 'application_status.status_id')
            ->select([DB::raw('CONCAT(persons.first_name, " ", persons.last_name) AS fullname'), 'companies.name as company', 'courses.name', 'intakes.intake_date', 'course_application.tuition_fee', 'course_application.course_application_id', 'application_status.status_id', 'users.email', 'phones.number'])
            ->orderBy('course_application.course_application_id', 'desc')
            ->find($course_application_id);

        return view('Tenant::ApplicationStatus/action/coe_issued', compact('applications'));
    }

    //updates for action_coe_issued
    public function update_coe_issued($course_application_id)
    {
        $updated = $this->application_status->coe_issued_update($this->request->all(), $course_application_id);
        if ($updated)
            $updated = $this->document->document_create($this->request->all());

        if ($updated)
            $updated = $this->application_status->coe_issued_create($this->request->all(), $course_application_id);

        Session::flash('success', 'Updated Successfully');
        return redirect()->route('applications.coe_issued.index');
    }


    //Information for coe issued page
    public function coeIssued()
    {
        $applications = $this->application_status->getApplications(5);
        return view('Tenant::ApplicationStatus/coe_issued', compact('applications'));
    }

    public function statusRecord($status_id)
    {
        $statusRecord = $this->application_status->statusRecord($status_id);
        return $statusRecord;
    }


} //controller ends here