<?php

namespace Modules\Product\DataTables;

use Modules\Product\Entities\Product;
use Modules\Product\Entities\Category;

use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ProductDataTable extends DataTable
{

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)->with('category')
            ->addColumn('action', function ($data) {
                return view('product::products.partials.actions', compact('data'));
            })
            ->addColumn('coin_in', function ($data) {

                return \DB::table('cme'.\Auth::user()->customers_id)->where('AssetId', $data->product_code)->where('EventType', 2)->sum('Arg2')/100;
            })
            ->addColumn('coin_out', function ($data) {
                return \DB::table('cme'.\Auth::user()->customers_id)->where('AssetId', $data->product_code)->where('EventType', 3)->sum('Arg2')/100;
            })

            ->rawColumns(['product_image']);
    }

    public function query(Product $model)
    {
        return $model->newQuery()->where('category_id', $this->site)->with('category');
    }

    public function html()
    {
        return $this->builder()
                    ->setTableId('product-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom("<'row'<'col-md-3'l><'col-md-5 mb-2'B><'col-md-4'f>> .
                                'tr' .
                                <'row'<'col-md-5'i><'col-md-7 mt-2'p>>")
                    ->orderBy(7)
                    ->buttons(
                        Button::make('excel')
                            ->text('<i class="bi bi-file-earmark-excel-fill"></i> Excel'),
                        Button::make('print')
                            ->text('<i class="bi bi-printer-fill"></i> Print'),
                        Button::make('reset')
                            ->text('<i class="bi bi-x-circle"></i> Reset'),
                        Button::make('reload')
                            ->text('<i class="bi bi-arrow-repeat"></i> Reload')
                    );
    }

    protected function getColumns()
    {
        return [

            Column::make('product_name')
                ->title('Name')
                ->className('text-center align-middle'),

            Column::make('product_code')
                ->title('Identifier')
                ->className('text-center align-middle'),

            Column::computed('product_price')
                ->title('Price')
                ->visible(false)

                ->className('text-center align-middle'),

            Column::computed('product_quantity')
                ->title('Quantity')
                ->visible(false)

                ->className('text-center align-middle'),
                

            Column::make('category.category_name')
                ->title('Site')
                
                ->className('text-center align-middle'),
            Column::make('product_order_tax')
                ->title('Revenue Share')
                ->className('text-center align-middle'),
            Column::computed('coin_in')
                ->title('Coin In')
                ->className('text-center align-middle'),
            Column::computed('coin_out')
                ->title('Coin Out')
                ->className('text-center align-middle'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->className('text-center align-middle'),

            Column::make('created_at')
                ->visible(false)
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Product_' . date('YmdHis');
    }
}
