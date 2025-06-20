<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\ProductJob;
use App\Product;
use App\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use File;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use DataTables;
use Carbon\Carbon;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category = Category::where('parent_id', '!=', null)->orderBy('name', 'ASC')->get();
        return view('products.index', compact('category'));
    }

    public function datatables(Request $request)
    {
        $products = Product::with('category')
            ->orderBy('created_at', 'DESC')
            ->get();

        return DataTables::of($products)
            ->addColumn('action', function ($product) use (&$index) {
                static $index = 0;
                $index++;

                return '
                    <a href="'. route('product.newEdit', $product->id) .'" class="btn btn-sm btn-primary" title="Edit Produk ' . $product->name . '"><span class="fa fa-pencil"></span></a>
                    <button type="button" class="btn btn-sm btn-danger delete-product ml-1" data-index="'.$index.'" data-product-id="'. $product->id .'" title="Hapus Produk ' . $product->name . '"><span class="fa fa-trash"></span></button>
                    <button type="button" class="btn btn-sm btn-info detail-product ml-1" data-index="'.$index.'" data-product-id="'. $product->id .'" title="Detail Produk ' . $product->name . '"><span class="fa fa-eye"></span></button>
 
                    <form id="deleteForm{{ $product->id }}" action="'. route('product.newDestroy', $product->id) .'" method="post" class="d-none">
                        '. method_field('DELETE') . csrf_field() .'
                    </form>
                ';
            })
            ->editColumn('productName', function($product){
                return $product->name . '<span class="ml-2">' . $product->status_type . '</span>';
            })
            ->editColumn('image', function ($product) {
                return '<img src="'. asset('/imageProducts/' . $product->image) .'" alt="'. $product->name .'" class="img-thumbnail rounded" style="width: 110px; height: 100px; object-fit: contain; display: block;">';
            })
            ->editColumn('description', function ($product) {
                return $product->description; 
            })
            ->editColumn('stock', function ($product) {
                return $product->stock . ' item'; 
            })
            ->editColumn('status', function ($product) {
                return $product->status_label; 
            })
            ->rawColumns(['action', 'image', 'description', 'status', 'stock', 'productName'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //QUERY UNTUK MENGAMBIL SEMUA DATA CATEGORY
        $category = Category::orderBy('name', 'DESC')->get();
        return view('products.create', compact('category'));
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
            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
                'description' => 'required',
                'category_id' => 'required|exists:categories,id',
                'status' => 'required',
                'price' => 'required',
                'weight' => 'required',
                'image' => 'required|image|mimes:png,jpeg,jpg,webp|max:2048'
            ]);

            // Check if the validation fails
            if ($validator->fails()) {
                return response()->json(['error' => 'Validasi gagal, Harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 422);
            }

            // Handle file upload
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('/imageProducts/');
                $file->move($destinationPath, $filename);

                // Create the product
                $product = Product::create([
                    'name' => $request->name,
                    'slug' => Str::slug($request->name),
                    'category_id' => $request->category_id,
                    'description' => $request->description,
                    'image' => $filename,
                    'price' => $request->price,
                    'weight' => $request->weight,
                    'status' => $request->status,
                ]);

                return response()->json(['success' => 'Produk berhasil ditambahkan'], 200);
            } else {
                return response()->json(['error' => 'Gagal mengunggah gambar'], 422);
            }

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
        $product = Product::findOrFail($id);

        if(!$product){
            return response()->json(['error' => 'Detail produk tidak ditemukan'], 404);
        }

        return response()->json([
            'name' => $product->name,
            'description' => $product->description,
            'category' => $product->category->name,
            'price' => 'Rp ' . number_format($product->price, 0, ',', '.'),
            'image' => asset('/imageProducts/' . $product->image),
            'status' => $product->status_label,
            'weight' => $product->weight . 'Gram / Kg'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::find($id); 
        $category = Category::orderBy('name', 'DESC')->get(); 
        return view('products.edit', compact('product', 'category')); 
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
            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
                'description' => 'required',
                'category_id' => 'required|exists:categories,id',
                'price' => 'required',
                'weight' => 'required',
                'image' => 'nullable|image|mimes:png,jpeg,jpg,webp|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'Validasi gagal, Harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 422);
            }
            
            // redirect to home if couldn't find id's product
            $product = Product::find($request->product_id);
            if (!$product) {
                return redirect()->back()->with('error', 'Produk tidak ditemukan.');
            }
            
            // get product
            $filename = $product->image;

            $currency = $request->price;
            $processedCurrency = intval(str_replace('.', '', str_replace(',', '.', $currency)));
        
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('/imageProducts/');
                $file->move($destinationPath, $filename);
    
                // hapus file lama
                File::delete($destinationPath . $product->image);
            }

            $product->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name), // Generate a slug for the name
                'description' => $request->description,
                'category_id' => $request->category_id,
                'price' => $processedCurrency,
                'weight' => $request->weight,
                'image' => $filename,
                'status' => $request->status,
            ]);
    
            return response()->json(['success' => 'Produk berhasil diperbarui'], 200);
        } catch (\Exception $e) {
            // Log the error message for debugging
            Log::error('Product Update Error: ' . $e->getMessage());
    
            // Return a JSON response with an error message
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
        $product = Product::find($id); 
        if (!$product) {
            return response()->json(['error' => 'Produk tidak ditemukan'], 404);
        }
        File::delete(public_path('/imageProducts/' . $product->image));
        $product->delete();
        return response()->json(['success' => 'Produk berhasil dihapus'], 200);
    }

    // public function massUploadForm()
    // {
    //     $category = Category::whereNotNull('parent_id')->orderBy('name', 'ASC')->get();
    //     return view('products.index', compact('category'));
    // }

    public function massUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'file' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation error, Harap isi field yang kosong.', 'errors' => $validator->errors(), 'input' => $request->all()
            ], 422);
        }

        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'Tidak ada file yang diupload'], 404);
        }

        $file = $request->file('file');
        $filename = time() . '-product.' . $file->getClientOriginalExtension();
        $destinationPath = public_path('/uploads/');
        $file->move($destinationPath, $filename);

        $directory = public_path('/imageProducts/');
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true, true);
        }

        try {
            $fileData = (new ProductImport)->toArray($destinationPath . $filename);
            $productData = [];
            $errors = [];
            $imageCache = [];

            foreach ($fileData as $sheet) {
                foreach ($sheet as $index => $row) {
                    // Skip rows with insufficient columns
                    if (count($row) < 6) {
                        $errors[] = "Row $index does not have enough columns";
                        continue;
                    }

                    $productName = $row[0];
                    $existingProduct = Product::where(function ($query) use ($productName) {
                            $query->where('name', $productName)
                                ->orWhere('slug', Str::slug($productName));
                        })
                        ->exists();

                    if ($existingProduct) {
                        $errors[] = "Product '{$productName}' already exists at row $index";
                        continue;
                    }

                    $imageUrl = trim($row[4]);
                    if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                        $errors[] = "Invalid URL at row $index";
                        continue;
                    }

                    // Check and save image from URL
                    if (!isset($imageCache[$imageUrl])) {
                        $imageContent = @file_get_contents($imageUrl);
                        if ($imageContent === false) {
                            $errors[] = "Failed to download image at row $index";
                            continue;
                        }

                        $imageFilename = Carbon::now()->format('YmdHis') . '-IMG-' . strtoupper(Str::random(6)) . '.' . pathinfo($imageUrl, PATHINFO_EXTENSION);
                        $imagePath = $directory . '/' . $imageFilename;

                        if (!@file_put_contents($imagePath, $imageContent)) {
                            $errors[] = "Failed to save image at row $index";
                            continue;
                        }

                        $imageCache[$imageUrl] = $imageFilename;
                    }

                    // Prepare product data for bulk insertion
                    $productData[] = [
                        'name' => $row[0],
                        'slug' => Str::slug($row[0]),
                        'category_id' => $request->category_id,
                        'description' => $row[1],
                        'price' => $row[2],
                        'weight' => $row[3],
                        'image' => $imageCache[$imageUrl],
                        'status' => true,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                }
            }

            // Batch insert data
            if (!empty($productData)) {
                Product::insert($productData);
            }

            File::delete($destinationPath . $filename);

            return response()->json(['success' => 'Berhasil menambahkan produk'], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
