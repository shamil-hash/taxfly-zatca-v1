<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\DB;
// use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\Importable;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    use Importable;

    protected $product_code;
    protected $userid;
    protected $branch;

    function __construct($product_code, $userid, $branch)
    {
        $this->product_code = $product_code;
        $this->userid = $userid;
        $this->branch = $branch;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        $unit = DB::table('units')
            ->where('unit', $row['unit'])
            ->where('branch_id', $this->branch)
            ->pluck('unit')
            ->first();

        if (!$unit) {
            $unit = Unit::firstOrCreate([
                'unit' => $row['unit'],
                'user_id' => $this->userid,
                'branch_id' => $this->branch,
            ])->unit;
        }

        $category_name = DB::table('categories')
            ->where('category_name', $row['category'])
            ->where('branch_id', $this->branch)
            ->pluck('category_name')
            ->first();

        if (!$category_name) {

            $category = new Category();
            $category->category_name = $row['category'];
            $category->user_id = $this->userid;
            $category->branch_id = $this->branch;
            $category->save();

            $category_id = $category->id;
        } else {
            $category_id = DB::table('categories')
                ->where('category_name', $row['category'])
                ->where('branch_id', $this->branch)
                ->pluck('id')
                ->first();
        }

        $p_id = DB::table('products')
            ->where('product_name', $row['product'])
            ->where('branch', $this->branch)
            ->pluck('id')
            ->first();
            
        /* automatic rate calculation */

        $rate = $row['rate'] ?? 0;
        $pur_vat = $row['purchase_vat'] ?? 0;

        $buycost = $row['buy_cost'] ?? 0;


        if (($buycost == '' || $buycost == null || $buycost == 0) && ($pur_vat == '' || $pur_vat == null || $pur_vat == 0)) {

            $rate = $row['rate'];
        } else if (($buycost != '' || $buycost != null || $buycost != 0) && ($pur_vat != '' || $pur_vat != null || $pur_vat != 0)) {

            // Calculate the buy cost
            $rate_calc = $buycost + ($buycost * $pur_vat / 100);

            $rate = round($rate_calc, 2);
        }
        /* -----------------------------*/
        
        
        /* automatic Inclusive Rate and Vat calculation */

        if (($row['sell_cost'] != '' || $row['sell_cost'] != null) && ($row['vat'] != '' || $row['vat'] != null)) {


            $inclusive_rate = $row['sell_cost'] / (1 + ($row['vat'] / 100));

            $inclusive_vat_amount = $row['sell_cost'] - $inclusive_rate;
        }

        /* --------------------------------------------*/
            
         $existingProduct = Product::where('product_name', $row['product'])
             ->where('branch', $this->branch)
            ->first();
            
            if(!($existingProduct)) {
                
                if (!isset($row['barcode'])) {
                   $this->product_code++;
                   
                    return new Product([
                       'product_name' => $row['product'],
                       'productdetails'  => $row['product_details'],
                       'unit'  => $unit,
                       'selling_cost'  => $row['sell_cost'],
                       'buy_cost'  => $row['buy_cost'],
                       'purchase_vat' => $row['purchase_vat'],
                       'rate' => $rate,
                       'inclusive_rate' => $inclusive_rate,
                       'inclusive_vat_amount' => $inclusive_vat_amount,
                       'user_id'  => $this->userid,
                       'branch'  => $this->branch,
                       'category_id'  => $category_id,
                       'vat'  => $row['vat'],
                       'barcode' => $this->product_code,
                    ]);
           
                } else {
                       return new Product([
                          'product_name' => $row['product'],
                          'productdetails'  => $row['product_details'],
                          'unit'  => $unit,
                          'selling_cost'  => $row['sell_cost'],
                          'buy_cost'  => $row['buy_cost'],
                          'purchase_vat' => $row['purchase_vat'],
                          'rate' => $rate,
                          'inclusive_rate' => $inclusive_rate,
                          'inclusive_vat_amount' => $inclusive_vat_amount,
                          'user_id'  => $this->userid,
                          'branch'  => $this->branch,
                          'category_id'  => $category_id,
                          'vat'  => $row['vat'],
                          'barcode' => (string) $row['barcode'],
                    ]);
                }
                
            } else if($existingProduct) {
                
                if (!isset($row['barcode'])) {
                   $this->product_code++;
                   
                    $existingProduct->update([
                       'product_name' => $row['product'],
                       'productdetails'  => $row['product_details'],
                       'unit'  => $unit,
                       'selling_cost'  => $row['sell_cost'],
                       'buy_cost'  => $row['buy_cost'],
                       'purchase_vat' => $row['purchase_vat'],
                       'rate' => $rate,
                       'inclusive_rate' => $inclusive_rate,
                       'inclusive_vat_amount' => $inclusive_vat_amount,
                       'user_id'  => $this->userid,
                       'branch'  => $this->branch,
                       'category_id'  => $category_id,
                       'vat'  => $row['vat'],
                       'barcode' => $this->product_code,
                    ]);
           
                } else {
                       $existingProduct->update([
                          'product_name' => $row['product'],
                          'productdetails'  => $row['product_details'],
                          'unit'  => $unit,
                          'selling_cost'  => $row['sell_cost'],
                          'buy_cost'  => $row['buy_cost'],
                          'purchase_vat' => $row['purchase_vat'],
                          'rate' => $rate,
                          'inclusive_rate' => $inclusive_rate,
                          'inclusive_vat_amount' => $inclusive_vat_amount,
                          'user_id'  => $this->userid,
                          'branch'  => $this->branch,
                          'category_id'  => $category_id,
                          'vat'  => $row['vat'],
                          'barcode' => (string) $row['barcode'],
                    ]);
                }
            }

        
       
    }
    // public function startRow(): int
    // {
    //     return 2;
    // }
    public function rules(): array
    {
        return [
            'product' => 'required',
            'unit' => 'required',
            'buy_cost' => 'required',
            'sell_cost' => 'required',
            'vat' => 'required',
            'category' => 'required',

        ];
    }
    // public function headingRow(): int
    // {
    //     return 1;
    // }
    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
