<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Models\AuditLog;
use App\Models\Subject;

class SubjectController extends Controller
{
    public function index(Request $request): void
    {
        $subjects = Subject::allWithClassCount();
        $this->view('admin/subjects/index', [
            'title' => 'Subject Management - Apex Attendance',
            'subjects' => $subjects,
        ]);
    }

    public function create(Request $request): void
    {
        $this->view('admin/subjects/create', [
            'title' => 'Create New Subject'
        ]);
    }

    public function store(Request $request): void
    {
        $errors = $this->validate($request->all(), [
            'subject_code' => 'required|min:2|max:30|unique:subjects,subject_code',
            'subject_name' => 'required|min:2|max:100',
        ]);

        if (!empty($errors)) {
            $this->flash('error', reset($errors)[0]);
            $this->redirect('/admin/subjects/create');
            return;
        }

        Subject::insert([
            'subject_code' => strtoupper(trim($request->input('subject_code'))),
            'subject_name' => trim($request->input('subject_name')),
            'description' => trim($request->input('description', '')),
            'status' => 'active',
        ]);

        AuditLog::log("Subject Created: {$request->input('subject_name')}", $this->userId());

        $this->flash('success', "Subject {$request->input('subject_name')} created successfully!");
        $this->redirect('/admin/subjects');
    }

    public function edit(Request $request, string $id): void
    {
        $subject = Subject::find((int)$id);
        if (!$subject) {
            $this->flash('error', 'Subject not found.');
            $this->redirect('/admin/subjects');
            return;
        }

        $this->view('admin/subjects/edit', [
            'title' => 'Edit Subject - ' . $subject['subject_name'],
            'subject' => $subject,
        ]);
    }

    public function update(Request $request, string $id): void
    {
        $subject = Subject::find((int)$id);
        if (!$subject) {
            $this->flash('error', 'Subject not found.');
            $this->redirect('/admin/subjects');
            return;
        }

        $errors = $this->validate($request->all(), [
            'subject_code' => "required|min:2|max:30|unique:subjects,subject_code,{$id},id",
            'subject_name' => 'required|min:2|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        if (!empty($errors)) {
            $this->flash('error', reset($errors)[0]);
            $this->redirect("/admin/subjects/{$id}/edit");
            return;
        }

        Subject::update((int)$id, [
            'subject_code' => strtoupper(trim($request->input('subject_code'))),
            'subject_name' => trim($request->input('subject_name')),
            'description' => trim($request->input('description', '')),
            'status' => $request->input('status'),
        ]);

        AuditLog::log("Subject Updated: {$request->input('subject_name')}", $this->userId());

        $this->flash('success', 'Subject updated successfully.');
        $this->redirect('/admin/subjects');
    }

    public function delete(Request $request, string $id): void
    {
        $subject = Subject::find((int)$id);
        if ($subject) {
            Subject::delete((int)$id);
            AuditLog::log("Subject Deleted: {$subject['subject_name']}", $this->userId());
            $this->flash('success', "Subject {$subject['subject_name']} deleted.");
        }
        $this->redirect('/admin/subjects');
    }
}
