@extends('layouts.app')

@section('title', 'Machine Details')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Machines</a></li>
        <li class="breadcrumb-item active">Details</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid mb-4">

        <div class="row">
            <div class="col-lg-12">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-0">
                                <tr>
                                    <th>Machine Identifier</th>
                                    <td>{{ $product->product_code }}</td>
                                </tr>

                                <tr>
                                    <th>Name</th>
                                    <td>{{ $product->product_name }}</td>
                                </tr>
                                <tr>
                                    <th>Site</th>
                                    <td>{{ $product->category->category_name }}</td>
                                </tr>

                                <tr>
                                    <th>Revenue Share</th>
                                    <td>{{ $product->product_order_tax }}</td>
                                </tr>
                                <tr>
                                    <th>Note</th>
                                    <td>{{ $product->product_note ?? 'N/A' }}</td>
                                </tr>

                                <tr>
                                    <th>Events</th>
                                    <td>No Recorded Events</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
@endsection



