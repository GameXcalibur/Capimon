<?php

namespace Modules\Product\Http\Controllers;

use Modules\Product\DataTables\ProductDataTable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Modules\Product\Entities\Product;
use Modules\Product\Http\Requests\StoreProductRequest;
use Modules\Product\Http\Requests\UpdateProductRequest;
use Modules\Upload\Entities\Upload;
use App\Models\CmEventType;
use App\Models\CmAsset;


use Carbon\Carbon;

class ProductController extends Controller
{

    public function index(ProductDataTable $dataTable) {
        abort_if(Gate::denies('access_products'), 403);

        return $dataTable->render('product::products.index');
    }


    public function indexSite(ProductDataTable $dataTable) {
        abort_if(Gate::denies('access_products'), 403);

        return $dataTable->render('product::products.index');
    }


    public function create() {
        abort_if(Gate::denies('create_products'), 403);

        return view('product::products.create');
    }


    public function store(StoreProductRequest $request) {
        //dd($request);
        $product = Product::create($request->except('document'));
        $asset_create = CmAsset::create([
            'CustomerId' => \Auth::user()->customers_id,
            'AssetId' => $request['product_code'],
            'Site' => $request['category_id'],
            'Type' => $request['product_name'],
            'RevenueShare' => $request['product_order_tax'],

        ]);
        if ($request->has('document')) {
            foreach ($request->input('document', []) as $file) {
                $product->addMedia(Storage::path('temp/dropzone/' . $file))->toMediaCollection('images');
            }
        }

        toast('Asset Created!', 'success');

        return redirect()->route('products.index');
    }


    public function show(Product $product) {
        abort_if(Gate::denies('show_products'), 403);
        $events = \DB::table('cme'.\Auth::user()->customers_id)->where('AssetId', $product->product_code)->get();
        $totalCoinIn = 0;
        $totalCoinOut = 0;
        $totalCoinRev = 0;
        $totalCoinRevSplit = 0;
        $lastCash = 0;
        $lastCashAmt = 0;
        $runningAmt = 0;


        foreach($events as &$event){
            if($event->EventType == 3){
                $totalCoinOut += $event->Arg2;

                $event->Arg2 = 'R'.$event->Arg2/100;
            }else if($event->EventType == 2 ){
                $totalCoinIn += $event->Arg2;

                $event->Arg2 = 'R'.$event->Arg2/100;

            }else{
                $event->Arg2 = '-';
            }
            $event->EventType = CmEventType::where('EventId', $event->EventType)->first()->EventName;
            $event->date = Carbon::parse($event->date)->addHours(2);

        }

        $totalCoinRev = $totalCoinIn - $totalCoinOut;
        $totalCoinRevSplit = ($product->product_order_tax/100)*$totalCoinRev;
        return view('product::products.show', compact('product', 'events', 'totalCoinIn', 'totalCoinOut', 'totalCoinRev', 'totalCoinRevSplit', 'lastCash', 'lastCashAmt', 'lastCashAmt', 'runningAmt'));
    }


    public function edit(Product $product) {
        abort_if(Gate::denies('edit_products'), 403);

        return view('product::products.edit', compact('product'));
    }


    public function update(UpdateProductRequest $request, Product $product) {
        $cmAsset = CmAsset::where('AssetId', $product->product_code)->first();
        $cmAsset->update([
            'AssetId' => $request['product_code'],
            'Site' => $request['category_id'],
            'Type' => $request['product_name'],
            'RevenueShare' => $request['product_order_tax'],
        ]);
        $product->update($request->except('document'));

        if ($request->has('document')) {
            if (count($product->getMedia('images')) > 0) {
                foreach ($product->getMedia('images') as $media) {
                    if (!in_array($media->file_name, $request->input('document', []))) {
                        $media->delete();
                    }
                }
            }

            $media = $product->getMedia('images')->pluck('file_name')->toArray();

            foreach ($request->input('document', []) as $file) {
                if (count($media) === 0 || !in_array($file, $media)) {
                    $product->addMedia(Storage::path('temp/dropzone/' . $file))->toMediaCollection('images');
                }
            }
        }

        toast('Asset Updated!', 'info');

        return redirect()->route('products.index');
    }


    public function destroy(Product $product) {
        abort_if(Gate::denies('delete_products'), 403);
        
        $cmAsset = CmAsset::where('AssetId', $product->product_code)->first();
        $cmAsset->delete();

        $product->delete();


        toast('Asset Deleted!', 'warning');

        return redirect()->route('products.index');
    }
}
