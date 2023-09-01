<?php

namespace App\Http\Controllers;

use App\Jobs\SalesCsvProcess;
use App\Models\Sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;

class SalesController extends Controller
{
    public function index()
    {
        return view('upload-file');
    }

    public function upload()
    {
        if (request()->has('mycsv')) {
            $data = file(request()->mycsv); // baca file csv
            $chunks = array_chunk($data, 1000); // foto menjadi per 1000

            $header = []; // buar variable yang manampung header
            $batch = Bus::batch([])->dispatch();

            foreach ($chunks as $key => $chunk) {
                $data = array_map('str_getcsv', $chunk); // buar array dari data potongan
                if ($key === 0) { // pengecekan jika header unset
                    $header = $data[0];
                    unset($data[0]);
                }
                $batch->add(new SalesCsvProcess($data, $header));
            }
            return $batch;
        }
        return 'please upload your';
    }

    public function batch()
    {
        $batchId = request()->id;

        return Bus::findBatch($batchId);
    }
}
