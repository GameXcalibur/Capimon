<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Expense\Entities\Expense;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\Category;

use App\Models\CmAsset;
use App\Models\CmEventType;
use App\Models\CmSite;



use Modules\Purchase\Entities\Purchase;
use Modules\Purchase\Entities\PurchasePayment;
use Modules\PurchasesReturn\Entities\PurchaseReturn;
use Modules\PurchasesReturn\Entities\PurchaseReturnPayment;
use Modules\Sale\Entities\Sale;
use Modules\Sale\Entities\SalePayment;
use Modules\SalesReturn\Entities\SaleReturn;
use Modules\SalesReturn\Entities\SaleReturnPayment;

class HomeController extends Controller
{

    public function index() {
        //$machines = CmAsset::where('CustomerId', \Auth::user()->customers_id)->get();

        $totalCoinIn = \DB::table('cme'.\Auth::user()->customers_id)->where('EventType', 2)->sum('Arg2');
        $totalCoinOut = \DB::table('cme'.\Auth::user()->customers_id)->where('EventType', 3)->sum('Arg2');


        $sales = Sale::completed()->sum('total_amount');
        $sale_returns = SaleReturn::completed()->sum('total_amount');
        $purchase_returns = PurchaseReturn::completed()->sum('total_amount');
        $product_costs = 0;

        foreach (Sale::completed()->with('saleDetails')->get() as $sale) {
            foreach ($sale->saleDetails as $saleDetail) {
                $product_costs += $saleDetail->product->product_cost;
            }
        }

        $revenue = ($sales - $sale_returns) / 100;
        $profit = $totalCoinIn - $totalCoinOut;

        $num_machines = CmAsset::count();
        $num_sites = Category::count();



        return view('home', [
            'revenue'          => $totalCoinIn/100,
            'num_machines'          => $num_machines,
            'num_sites'          => $num_sites,

            'sale_returns'     => $totalCoinOut / 100,
            'purchase_returns' => $purchase_returns / 100,
            'profit'           => $profit/100
        ]);
    }


    public function currentMonthChart() {
        abort_if(!request()->ajax(), 404);


        $totalCoinIn = \DB::table('cme'.\Auth::user()->customers_id)->where('EventType', 2)
        ->whereMonth('date', date('m'))
                ->whereYear('date', date('Y'))
                ->sum('Arg2') / 100;

        $totalCoinOut = \DB::table('cme'.\Auth::user()->customers_id)->where('EventType', 3)
        ->whereMonth('date', date('m'))
                ->whereYear('date', date('Y'))
                ->sum('Arg2') / 100;

        $currentMonthExpenses = Expense::whereMonth('date', date('m'))
                ->whereYear('date', date('Y'))
                ->sum('amount') / 100;

        return response()->json([
            'sales'     => $totalCoinIn,
            'purchases' => $totalCoinOut,
            'expenses'  => $currentMonthExpenses
        ]);
    }


    public function salesPurchasesChart() {
        abort_if(!request()->ajax(), 404);

        $sales = $this->salesChartData();
        $purchases = $this->purchasesChartData();

        return response()->json(['sales' => $sales, 'purchases' => $purchases]);
    }


    public function paymentChart() {
        abort_if(!request()->ajax(), 404);

        $dates = collect();
        foreach (range(-11, 0) as $i) {
            $date = Carbon::now()->addMonths($i)->format('m-Y');
            $dates->put($date, 0);
        }

        $date_range = Carbon::today()->subYear()->format('Y-m-d');
        $totalCoinIn = \DB::table('cme'.\Auth::user()->customers_id)->where('EventType', 2)
        ->select([
            DB::raw("DATE_FORMAT(date, '%m-%Y') as month"),
            DB::raw("SUM(Arg2) as amount")
        ])
        ->groupBy('month')->orderBy('month')
        ->get()->pluck('amount', 'month');


        $totalCoinOut = \DB::table('cme'.\Auth::user()->customers_id)->where('EventType', 3)
        ->select([
            DB::raw("DATE_FORMAT(date, '%m-%Y') as month"),
            DB::raw("SUM(Arg2) as amount")
        ])
        ->groupBy('month')->orderBy('month')
        ->get()->pluck('amount', 'month');



        $dates_received = $dates->merge($totalCoinIn);
        $dates_sent = $dates->merge($totalCoinOut);

        $received_payments = [];
        $sent_payments = [];
        $months = [];

        foreach ($dates_received as $key => $value) {
            $received_payments[] = $value/100;
            $months[] = $key;
        }

        foreach ($dates_sent as $key => $value) {
            $sent_payments[] = $value/100;
        }

        return response()->json([
            'payment_sent' => $sent_payments,
            'payment_received' => $received_payments,
            'months' => $months,
        ]);
    }

    public function salesChartData() {
        $dates = collect();
        foreach (range(-6, 0) as $i) {
            $date = Carbon::now()->addDays($i)->format('d-m-y');
            $dates->put($date, 0);
        }

        $date_range = Carbon::today()->subDays(6);
        $totalCoinIn = \DB::table('cme'.\Auth::user()->customers_id)->where('EventType', 2)
        ->where('date', '>=', $date_range)
        ->groupBy(DB::raw("DATE_FORMAT(date,'%d-%m-%y')"))
        ->orderBy('date')
        ->get([
            DB::raw(DB::raw("DATE_FORMAT(date,'%d-%m-%y') as date")),
            DB::raw('SUM(Arg2) AS count'),
        ])
        ->pluck('count', 'date');




        $dates = $dates->merge($totalCoinIn);

        $data = [];
        $days = [];
        foreach ($dates as $key => $value) {
            $data[] = $value / 100;
            $days[] = $key;
        }

        return response()->json(['data' => $data, 'days' => $days]);
    }


    public function purchasesChartData() {
        $dates = collect();
        foreach (range(-6, 0) as $i) {
            $date = Carbon::now()->addDays($i)->format('d-m-y');
            $dates->put($date, 0);
        }

        $date_range = Carbon::today()->subDays(6);

        $totalCoinOut = \DB::table('cme'.\Auth::user()->customers_id)->where('EventType', 3)
        ->where('date', '>=', $date_range)
        ->groupBy(DB::raw("DATE_FORMAT(date,'%d-%m-%y')"))
        ->orderBy('date')
        ->get([
            DB::raw(DB::raw("DATE_FORMAT(date,'%d-%m-%y') as date")),
            DB::raw('SUM(Arg2) AS count'),
        ])
        ->pluck('count', 'date');


        $dates = $dates->merge($totalCoinOut);

        $data = [];
        $days = [];
        foreach ($dates as $key => $value) {
            $data[] = $value / 100;
            $days[] = $key;
        }

        return response()->json(['data' => $data, 'days' => $days]);

    }
}
