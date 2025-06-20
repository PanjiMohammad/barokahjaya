<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Category;
use DataTables;
use Carbon\Carbon;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {      
        $parent = Category::getParent()->orderBy('name', 'ASC')->get();
        return view('categories.index', compact('parent'));
    }

    public function getDatatables(Request $request){
        $category = Category::with(['parent'])->orderBy('created_at', 'ASC');

        return DataTables::of($category)
            ->addColumn('action', function ($cat) {
                return '
                    <button type="button" class="btn btn-sm btn-primary edit-category" data-category-id="'. $cat->id .'"><span class="fa fa-pencil"></span></button>
                    <button type="button" class="btn btn-sm btn-danger delete-category" data-category-id="'. $cat->id .'" data-category-name="' . $cat->name . '"><span class="fa fa-trash"></span></button>
                    <form id="deleteForm{{ $cat->id }}" action="'. route('category.destroy', $cat->id) .'" method="post" class="d-none">
                        '. method_field('DELETE') . csrf_field() .'
                    </form>
                ';
            })
            ->addColumn('parent_name', function ($cat) {
                return $cat->parent ? $cat->parent->name : '-';
            })
            ->addColumn('formattedDate', function($cat) {
                return Carbon::parse($cat->created_at)->locale('id')->translatedFormat('l, d F Y');
            })
            ->rawColumns(['action', 'parent_name', 'formattedDate'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50|unique:categories',
                'parent_id' => 'nullable'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'Validasi gagal, Harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 422);
            }

            $request->request->add(['slug' => Str::slug($request->name)]);
            $category = Category::create($request->except('_token'));

            return response()->json(['success' => 'Berhasil menambahkan kategori baru', 'category' => $category], 200);
        } catch (\Exception $e) {
            // Catch any exception that occurs and return an error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::find($id); 
        $parent = Category::getParent()->orderBy('name', 'ASC')->get(); 
        
        return response()->json(['category' => $category, 'parent' => $parent]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'parent_id' => 'nullable|exists:categories,id'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'Validasi gagal, Harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 422);
            }

            $category = Category::findOrFail($request->category_id);
            if (!$category) {
                return response()->json(['error' => 'Kategori tidak ditemukan.'], 404);
            }

            $category->name = $request->name;
            $category->slug = Str::slug($request->name);
            $category->parent_id = $request->parent_id;
            $category->save();

            return response()->json(['success' => 'Kategori berhasil diperbaharui', 'category' => $category], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $category = Category::withCount(['child', 'product'])->findOrFail($id);
            
            if ($category->child_count == 0 && $category->product_count == 0) {
                $category->delete();
                return response()->json(['success' => true, 'message' => 'Kategori berhasil dihapus'], 200);
            }

            return response()->json(['success' => false, 'message' => 'Kategori Ini Memiliki Anak Kategori atau Produk'], 409);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Kategori tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' => 'Terjadi kesalahan, silakan coba lagi nanti.'], 500);
        }
    }
}
