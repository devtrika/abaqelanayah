<?php
namespace App\Http\Controllers\Admin;

use App\Exports\CategoryExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\categories\Store;
use App\Http\Requests\Admin\categories\Update;
use App\Models\Category;
use App\Traits\Report;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CategoryController extends Controller
{
    public function index($id = null)
    {
        if (request()->ajax()) {

            $categories = Category::search(request()->searchArray)->paginate(30);

            $html = view('admin.categories.table', compact('categories'))->render();
            return response()->json(['html' => $html]);
        }

        $categories = Category::latest()->get();
        return view('admin.categories.index', compact('categories', 'id'));
    }

    public function export()
    {
        return Excel::download(new CategoryExport, 'users.xlsx');
    }

    public function create($id = null)
    {
        $categories = Category::where('is_active', 1)->whereNull('parent_id')->get();
        return view('admin.categories.create', compact('categories'));
    }

    public function store(Store $request)
    {
    $category = Category::create($request->validated());

        // Handle image upload using Spatie Media Library
        if ($request->hasFile('image')) {
            $category->addMediaFromRequest('image')
                ->usingName('category_image')
                ->usingFileName(time() . '_category_' . $category->id . '.' . $request->file('image')->getClientOriginalExtension())
                ->toMediaCollection('categories');
        }

        Report::addToLog('اضافه قسم');
        return response()->json(['url' => route('admin.categories.index')]);
    }
    public function edit($id)
    {
        $category   = Category::findOrFail($id);
        $categories = Category::where('is_active', 1)->whereNull('parent_id')->get();
        return view('admin.categories.edit', ['category' => $category, 'categories' => $categories]);
    }

    public function update(Update $request, $id)
    {
        $category = Category::findOrFail($id);
        $category->update($request->validated());

        // Handle image upload using Spatie Media Library
        if ($request->hasFile('image')) {
            // Clear existing media in the collection
            $category->clearMediaCollection('categories');

            // Add new media
            $category->addMediaFromRequest('image')
                ->usingName('category_image')
                ->usingFileName(time() . '_category_' . $category->id . '.' . $request->file('image')->getClientOriginalExtension())
                ->toMediaCollection('categories');
        }

        Report::addToLog('  تعديل قسم');
        return response()->json(['url' => route('admin.categories.index')]);
    }

    public function show($id)
    {
        $category   = Category::findOrFail($id);
        $categories = Category::all();
        return view('admin.categories.show', ['category' => $category, 'categories' => $categories]);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id)->delete();
        Report::addToLog('  حذف قسم');
        return response()->json(['id' => $id]);
    }

    public function destroyAll(Request $request)
    {
        $requestIds = json_decode($request->data);

        foreach ($requestIds as $id) {
            $ids[] = $id->id;
        }
        if (Category::whereIntegerInRaw('id', $ids)->get()->each->delete()) {
            Report::addToLog('  حذف العديد من الاقسام');
            return response()->json('success');
        } else {
            return response()->json('failed');
        }
    }
}
