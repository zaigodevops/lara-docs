<?php

namespace ZaigoInfotech\LaraDocs\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use ZaigoInfotech\LaraDocs\Models\Document;
use ZaigoInfotech\LaraDocs\Models\DocumentCategory;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $category = DocumentCategory::where('status', '1')->get();
        return view('lara-docs::document.index')->with(['category' => $category]);
    }

    public function list(Request $request)
    {
        $search = $request->query('search', '');
        $type = $request->query('type', '');
        $status = $request->query('status', '');
        $categoryId = $request->query('category', '');

        $categories = DocumentCategory::all();

        $documents = Document::with('category')
            ->orderByDesc('created_at');

        if (!empty($search)) {
            $documents = $documents->where(function ($query) use ($search) {
                $query->where('files', 'LIKE', "%{$search}%")
                    ->orWhereHas('category', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        if (!empty($type)) {
            $documents = $documents->where('file_type', 'LIKE', "%{$type}%");
        }

        if (!empty($categoryId)) {
            $documents = $documents->whereHas('category', function ($query) use ($categoryId) {
                $query->where('id', $categoryId);
            });
        }

        if ($request->has('status') && $status !== '') {
            $documents = $documents->whereHas('category', function ($query) use ($status) {
                $query->where('status', $status);
            });
        }

        $documents = $documents->paginate(10)->appends([
            'search' => $search,
            'type' => $type,
            'status' => $status,
            'category' => $categoryId,
        ]);

        foreach ($documents as $document) {
            $document->category_name = $document->category ? $document->category->name : 'Unknown Category';
            $document->files = pathinfo($document->files, PATHINFO_FILENAME);
        }

        return view('lara-docs::document.list', [
            'document' => $documents,
            'categories' => $categories,
        ]);
    }


    public function upload(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'documents' => 'required|array',
            'documents.*' => 'mimes:pdf,doc,docx|max:5120',
        ]);

        // Ensure the 'public/documents' directory exists
        $documentsPath = public_path('documents');
        if (!is_dir($documentsPath)) {
            mkdir($documentsPath, 0755, true); // Creates the directory with appropriate permissions
        }

        $categoryIds = is_array($request->category_id) ? $request->category_id : explode(',', $request->category_id);

        foreach ($categoryIds as $categoryId) {
            foreach ($request->file('documents') as $file) {
                $filename = $file->getClientOriginalName();
                $filePath = $file->storeAs('documents', $filename, 'public');

                $fileType = $file->getClientOriginalExtension();

                $document = new Document();
                $document->category_id = $categoryId;
                $document->files = $filePath;
                $document->file_type = $fileType;
                $document->save();
            }
        }

        return redirect()->route('document.list')->with('success', 'Documents uploaded successfully!');
    }



    public function viewdocument(Request $request)
    {
        $categoryId = $request->category_id;
        $documentId = $request->id;

        $document = Document::where('category_id', $categoryId)
            ->where('id', $documentId)
            ->first();

        if ($document) {
            $filename = $document->files;
            $filePath = storage_path('app/public/' . $filename);

            if (!file_exists($filePath)) {
                return redirect()->back()->with('error', 'File does not exist.');
            }

            // Generate the file URL
            $fileUrl = asset('storage/' . $filename);

            return response()->json(['url' => $fileUrl]);
        }
        return redirect()->back()->with('error', 'Document not found.');
    }


    public function downloaddocument(Request $request)
    {
        $categoryId = $request->category_id;
        $documentId = $request->document_id;

        $document = Document::where('category_id', $categoryId)
            ->where('id', $documentId)
            ->first();

        if ($document) {
            // Get the filename
            $filename = basename($document->files); // This will get the file name without the path

            // Build the file path
            $filePath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $document->files);

            // Check if file exists before attempting to download
            if (file_exists($filePath)) {
                return response()->download($filePath, $filename);
            } else {
                return redirect()->back()->with('error', 'File does not exist.');
            }
        }

        return redirect()->back()->with('error', 'Document not found.');
    }

    public function deletedocument(Request $request)
    {
        $document = Document::where('id', $request->documentId)
            ->where('category_id', $request->categoryId)
            ->first();
        if ($document) {
            $document->delete();
            return redirect()->back()->with('success', 'Payslip deleted successfully.');
        }

        return redirect()->back()->with('error', 'Payslip does not exist.');
    }
}