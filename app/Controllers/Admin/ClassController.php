<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Models\AuditLog;
use App\Models\SchoolClass;

class ClassController extends Controller
{
    public function index(Request $request): void
    {
        $classes = SchoolClass::allWithStats();
        $this->view('admin/classes/index', [
            'title' => 'Class Management - Apex Attendance',
            'classes' => $classes,
        ]);
    }

    public function create(Request $request): void
    {
        $this->view('admin/classes/create', [
            'title' => 'Create New Class'
        ]);
    }

    public function store(Request $request): void
    {
        $errors = $this->validate($request->all(), [
            'class_name' => 'required|min:2|max:100',
            'class_code' => 'required|min:2|max:30|unique:classes,class_code',
            'start_time' => 'required',
            'late_grace_minutes' => 'required|numeric',
            'rest_start_time' => 'required',
            'rest_end_time' => 'required',
        ]);

        if (!empty($errors)) {
            $this->flash('error', reset($errors)[0]);
            $this->redirect('/admin/classes/create');
            return;
        }

        $id = SchoolClass::insert([
            'class_name' => trim($request->input('class_name')),
            'class_code' => strtoupper(trim($request->input('class_code'))),
            'start_time' => $request->input('start_time'),
            'late_grace_minutes' => (int)$request->input('late_grace_minutes'),
            'rest_start_time' => $request->input('rest_start_time'),
            'rest_end_time' => $request->input('rest_end_time'),
            'status' => 'active',
        ]);

        AuditLog::log("Class Created: {$request->input('class_name')}", $this->userId());

        $this->flash('success', "Class {$request->input('class_name')} created successfully!");
        $this->redirect('/admin/classes');
    }

    public function edit(Request $request, string $id): void
    {
        $class = SchoolClass::find((int)$id);
        if (!$class) {
            $this->flash('error', 'Class not found.');
            $this->redirect('/admin/classes');
            return;
        }

        $this->view('admin/classes/edit', [
            'title' => 'Edit Class - ' . $class['class_name'],
            'class' => $class,
        ]);
    }

    public function update(Request $request, string $id): void
    {
        $class = SchoolClass::find((int)$id);
        if (!$class) {
            $this->flash('error', 'Class not found.');
            $this->redirect('/admin/classes');
            return;
        }

        $errors = $this->validate($request->all(), [
            'class_name' => 'required|min:2|max:100',
            'class_code' => "required|min:2|max:30|unique:classes,class_code,{$id},id",
            'start_time' => 'required',
            'late_grace_minutes' => 'required|numeric',
            'rest_start_time' => 'required',
            'rest_end_time' => 'required',
            'status' => 'required|in:active,inactive',
        ]);

        if (!empty($errors)) {
            $this->flash('error', reset($errors)[0]);
            $this->redirect("/admin/classes/{$id}/edit");
            return;
        }

        SchoolClass::update((int)$id, [
            'class_name' => trim($request->input('class_name')),
            'class_code' => strtoupper(trim($request->input('class_code'))),
            'start_time' => $request->input('start_time'),
            'late_grace_minutes' => (int)$request->input('late_grace_minutes'),
            'rest_start_time' => $request->input('rest_start_time'),
            'rest_end_time' => $request->input('rest_end_time'),
            'status' => $request->input('status'),
        ]);

        AuditLog::log("Class Updated: {$request->input('class_name')}", $this->userId());

        $this->flash('success', 'Class updated successfully.');
        $this->redirect('/admin/classes');
    }

    public function delete(Request $request, string $id): void
    {
        $class = SchoolClass::find((int)$id);
        if ($class) {
            SchoolClass::delete((int)$id);
            AuditLog::log("Class Deleted: {$class['class_name']}", $this->userId());
            $this->flash('success', "Class {$class['class_name']} deleted.");
        }
        $this->redirect('/admin/classes');
    }
}
