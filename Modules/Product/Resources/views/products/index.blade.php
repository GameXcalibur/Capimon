@extends('layouts.app')

@section('title', 'Assets')

@section('third_party_stylesheets')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('product-categories.index') }}">Sites</a></li>
        <li class="breadcrumb-item active">Assets</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <hr>
                            <div class="row justify-content-center" style='text-align:center;'>
                                <h1>SITE BREAKDOWN</h1>
                            </div>
                            <div class="row justify-content-center" style='text-align:center;'>
                                <h1>TOAL IN: <b>{{$coinIn}}</b></h1>
                            </div>
                            <div class="row justify-content-center" style='text-align:center;'>
                                <h1>TOAL OUT: <b>{{$coinOut}}</b></h1>
                            </div>
                        <hr>
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createAssetModal">
                            Add Asset <i class="bi bi-plus"></i>
                        </button>

                        <hr>

                        <div class="table-responsive">
                            {!! $dataTable->table() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
     <!-- Create Modal -->
    <div class="modal fade" id="createAssetModal" tabindex="-1" role="dialog" aria-labelledby="createAssetModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createAssetModalLabel">Add Asset</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="product-form" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="product_name">Asset Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="product_name" required value="{{ old('product_name') }}">
                        </div>
                        <div class="form-group">
                            <label for="product_code">Asset Identifier <span class="text-danger">*</span></label>
                            <input type="text" maxlength='10' class="form-control" name="product_code" style="text-transform: uppercase" placeholder='XXX-XXX-XX' id='product_code' onkeydown='product_key_down();' onkeyup='product_key_up();' autocomplete="off" required value="{{ old('product_code') }}">
                        </div>
                        <div class="form-group">
                            <label for="product_order_tax">Revenue Share <span class="text-danger">*</span></label>
                            <input type="text"  class="form-control" name="product_order_tax" required value="{{ old('product_order_tax') }}">
                        </div>
                        <div class="form-group">
                            <label for="product_note">Note</label>
                            <textarea name="product_note" id="product_note" rows="4 " class="form-control"></textarea>
                        </div>
                            <input type="hidden" name="category_id" value="{{$site_id}}">
                            <input type="hidden" name="product_cost" value="100" >
                            <input type="hidden" name="product_price" value="100" >
                            <input type="hidden" name="product_quantity" value="10000" >
                            <input type="hidden" name="product_unit" value="EA" >
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Add <i class="bi bi-check"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection



@push('page_scripts')
    {!! $dataTable->scripts() !!}

<script>

var globText;

function product_key_down() {

  //  var key = event.keyCode || event.charCode;
  //  if( key == 8 || key == 46 )
  //      return false;

    var textLength = document.getElementById('product_code').value.length;

    if(textLength == 0) {
        document.getElementById('product_code').style.border = "";
        globText = "";
    }

}

function product_key_up() {

    var key = event.keyCode || event.charCode;
    if( key == 8 || key == 46 ){

        globText = globText.substring(0, globText.length - 1);
        if((globText.length  == 3) || (globText.length  == 7)){
            globText = globText.substring(0, globText.length - 1);
        }
        document.getElementById('product_code').value = globText;
        return false;
    }

    var t = document.getElementById('product_code').value;
    var pKeyBox = document.getElementById('product_code');

    var globTextLength = globText.length;

    if(globTextLength < 10){
        globText += t.slice(-1).toUpperCase();
        globTextLength += 1;
        if((globTextLength == 3) || (globTextLength == 7)){
            globText += "-";
        }
        if(globTextLength == 10){
            if(!product_key_validate()){
                alert("Not a valid asset identifier!\n Please retype it.");
                pKeyBox.style.border = "red solid 3px";
                globText = "";
            }
        }
    }

    document.getElementById('product_code').value = globText;

}

function product_key_validate() {
 
    let pKeyVal = globText;
    let keyMask = /^([A-Z]{3})-([0-9]{3})-([A-Z]{2})$/;
        
    if(keyMask.test(pKeyVal)) {
        return true;
    } else {
        return false;
    }
}

</script>
@endpush
