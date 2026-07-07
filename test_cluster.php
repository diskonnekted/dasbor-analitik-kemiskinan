<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$c = new \App\Http\Controllers\ClusterController();

// Simulate just the dataset logic
        $maxTahun = \App\Models\KesejahteraanKecamatan::max('tahun');
        $kesejahteraan = \App\Models\KesejahteraanKecamatan::where('tahun', $maxTahun)->get();
        
        $datasetMap = [];
        foreach ($kesejahteraan as $k) {
            $kec = strtoupper(trim($k->kecamatan));
            
            if (in_array($kec, ['PUWONEGORO', 'PURWONEGORO'])) {
                $kec = 'PURWANEGARA';
            }

            if (isset($datasetMap[$kec])) {
                continue;
            }

            $datasetMap[$kec] = [
                'kecamatan' => $kec,
            ];
        }
echo "Keys in DatasetMap: " . implode(", ", array_keys($datasetMap)) . "\n\n";

$view = $c->index();
$data = $view->getData();
echo json_encode($data['chartDatasets'][0]['data'], JSON_PRETTY_PRINT);
