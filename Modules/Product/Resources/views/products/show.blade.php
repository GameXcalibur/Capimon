@extends('layouts.app')

@section('title', 'Asset Details')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('index.site', ['site' => $product->category->id]) }}">Assets</a></li>
        <li class="breadcrumb-item active">Details</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid mb-4">

        <div class="row">
            <div class="col-lg-12" style="line-height: 0.5">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-0">
                                <tr>
                                    <th>Asset Identifier</th>
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
                                    <th>Lifetime Coin In</th>
                                    <td>R{{ $totalCoinIn/100 }}</td>
                                </tr>

                                <tr>
                                    <th>Lifetime Coin Out</th>
                                    <td>R{{ $totalCoinOut/100 }}</td>
                                </tr>

                                <tr>
                                    <th>Lifetime Revenue / Revenue After Split</th>
                                    <td>R{{ $totalCoinRev/100 }} / R{{ $totalCoinRevSplit/100 }}</td>
                                </tr>
                                <tr>
                                    <th>Last Cash Up / Amount</th>
                                    <td>R{{ $lastCash }} / R{{ $lastCashAmt }}</td>
                                </tr>
                                <tr>
                                    <th>Running Asset Revenue</th>
                                    <td>R{{ $totalCoinRev/100 }}</td>
                                </tr>
                                <tr>
                                    <th>Note</th>
                                    <td>{{ $product->product_note ?? 'N/A' }}</td>
                                </tr>

                                <tr>
                                    <th>Events</th>
                                    @if (!$events)
                                        <td>No Recorded Events</td>
                                    @endif
                                </tr>
                                @if ($events)
                                @foreach ($events as $event)

                                <tr>
                                    <th>{{$event->date}}</th>
                                            <td>{{$event->EventType}} : {{$event->Arg2}}</td>       
                                </tr>
                                @endforeach

                                @endif

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



