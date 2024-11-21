<?php

namespace DMS\DocumentManagementSystem\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use DMS\DocumentManagementSystem\Models\Document;
use Dms\DocumentManagementSystem\Models\DocumentCategory;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $status = $request->query('status', '');
        $date = $request->query('date', '');

        $categoryQuery = DocumentCategory::query();

        if (!empty($search)) {
            $categoryQuery->where('name', 'LIKE', "%{$search}%");
        }

        if ($request->has('status') && $status !== '') {
            $categoryQuery->where('status', $status);
        }

        if (!empty($date)) {
            $formattedDate = \Carbon\Carbon::createFromFormat('m/d/Y', $date)->format('Y-m-d');
            $categoryQuery->whereDate('created_at', $formattedDate);
        }

        $categories = $categoryQuery->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.category.list')->with(['category' => $categories]);
    }

    public function create(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|min:3|max:255|unique:document_categories,name',
            ],
                [
                    'name.required' => 'The category name field is required.',
                    'name.min' => 'The category name must be at least :min characters.',
                    'name.max' => 'The category name may not be greater than :max characters.',
                    'name.unique' => 'The category name has already been taken.',
            ]);

            $category = new DocumentCategory();
            $category->name = $request->name;
            $category->save();

            return response()->json(['message' => 'Category created successfully!'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|min:3|max:255|unique:document_categories,name,' . $id,
                'status' => 'required|boolean',
            ], [
                'name.required' => 'The category name field is required.',
                'name.min' => 'The category name must be at least :min characters.',
                'name.max' => 'The category name may not be greater than :max characters.',
                'name.unique' => 'The category name has already been taken.',
                'status.required' => 'The status field is required.',
                'status.boolean' => 'The status field must be true or false.',
            ]);

            $category = DocumentCategory::findOrFail($id);
            $category->name = $request->name;
            $category->status = $request->status;
            $category->save();
            return response()->json(['message' => 'Category updated successfully!']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function delete(Request $request, $id)
    {
        $category = DocumentCategory::findOrFail($id);
        if ($category) {
            $category->status = '0';
            $category->save();
            return response()->json(['message' => 'Category removed successfully!']);
        }
        return response()->json(['message' => 'Category not removed']);
    }

}