<?php

namespace DMS\DocumentManagementSystem\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use DMS\DocumentManagementSystem\Models\Document;
use Dms\DocumentManagementSystem\Models\DocumentCategory;

class DashboardController extends Controller
{
    public function document(Request $request)
    {
        $search = $request->query('search', '');
        $type = $request->query('type', '');
        $categoryId = $request->query('category', '');
    
        $categories = DocumentCategory::where('status' , 1)->get();
    
        $documents = Document::with('category')
            ->whereHas('category', function ($query) {
                $query->where('status', 1); 
            })
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
    
        $documents = $documents->paginate(10)->appends([
            'search' => $search,
            'type' => $type,
            'category' => $categoryId,
        ]);
    
        foreach ($documents as $document) {
            $document->category_name = $document->category ? $document->category->name : 'Unknown Category';
            $document->files = pathinfo($document->files, PATHINFO_FILENAME);
        }
    
        return view('user.documentlist', [
            'document' => $documents,
            'categories' => $categories,
        ]);
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
}