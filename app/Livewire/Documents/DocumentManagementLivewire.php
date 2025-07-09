<?php

namespace App\Livewire\Documents;

use App\Models\Document;
use App\Models\Application;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class DocumentManagementLivewire extends Component
{
    use LivewireAlert;

    public $modal = false;
    public $documents;
    public $selectedDocument;

    // form fields
    public $application_id, $file_path, $file_type;

    public function create($id = null)
    {
        if (empty($id)) {
            $this->reset(['selectedDocument', 'application_id', 'file_path', 'file_type']);
            $this->modal = true;
        } else {
            $this->selectedDocument = Document::findOrFail($id);
            $this->application_id = $this->selectedDocument->application_id;
            $this->file_path = $this->selectedDocument->file_path;
            $this->file_type = $this->selectedDocument->file_type;
            $this->modal = true;
        }
    }

    public function store()
    {
        $rules = [
            'application_id' => 'required|exists:applications,id',
            'file_path' => 'required|string|max:255',
            'file_type' => 'required|string|max:255',
        ];

        $this->validate($rules);

        if (empty($this->selectedDocument)) {
            Document::create([
                'application_id' => $this->application_id,
                'file_path' => $this->file_path,
                'file_type' => $this->file_type,
            ]);
            $this->alert('success', 'Document created successfully.');
        } else {
            $this->selectedDocument->update([
                'application_id' => $this->application_id,
                'file_path' => $this->file_path,
                'file_type' => $this->file_type,
            ]);
            $this->alert('success', 'Document updated successfully.');
        }

        $this->cancel();
    }

    public function delete($id)
    {
        $document = Document::findOrFail($id);
        $document->delete();
        $this->alert('success', 'Document deleted successfully.');
    }

    public function confirmDelete($id)
    {
        $this->confirm('Are you sure you want to delete this document?', [
            'onConfirmed' => 'delete',
            'params' => $id,
        ]);
    }

    public function cancel()
    {
        $this->reset(['modal', 'selectedDocument', 'application_id', 'file_path', 'file_type']);
        $this->dispatch('modal-cancel');
    }

    public function render()
    {
        $this->documents = Document::with('application')->get();
        return view('livewire.documents.document-management-livewire', [
            'applications' => Application::all(),
        ]);
    }
}