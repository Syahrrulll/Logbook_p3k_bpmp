<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$targetDate = '2026-05-23';
$logsOnDate = App\Models\Logbook::whereDate('tanggal', $targetDate)->get();
echo "Total logbooks on {$targetDate}: " . $logsOnDate->count() . "\n";
foreach ($logsOnDate as $log) {
    echo "- ID:" . $log->id . " user:" . $log->user_id . " lokasi:" . $log->lokasi . " kegiatan:" . $log->kegiatan . " sasaran:" . $log->sasaran_pekerjaan . " is_duplicate:" . ($log->is_duplicate ? '1' : '0') . "\n";
}

$svc = app('App\\Services\\LogbookAiService');

$lokasiLogs = App\Models\Logbook::where('user_id', 5)
    ->whereDate('tanggal', $targetDate)
    ->where('lokasi', 'Aula BPMP')
    ->get();
echo "\nAula BPMP entries for user 5 on {$targetDate}: " . $lokasiLogs->count() . "\n";
foreach ($lokasiLogs as $log) {
    echo "- ID:" . $log->id . " kegiatan:" . $log->kegiatan . " sasaran:" . $log->sasaran_pekerjaan . " is_duplicate:" . ($log->is_duplicate ? '1' : '0') . "\n";
}

if ($lokasiLogs->count() > 1) {
    $method = new ReflectionMethod(App\Services\LogbookAiService::class, 'calculateSimilarity');
    $method->setAccessible(true);
    for ($i = 0; $i < $lokasiLogs->count(); $i++) {
        for ($j = $i + 1; $j < $lokasiLogs->count(); $j++) {
            $first = $lokasiLogs[$i];
            $second = $lokasiLogs[$j];
            echo "\nSimilarity between ID {$first->id} and ID {$second->id}: " . $method->invoke($svc, $first->kegiatan, $second->kegiatan) . "\n";
        }
    }
}

echo "\nTesting duplicate detection for a new entry...\n";
$uid = App\Models\User::where('email', 'not like', '%admin%')->value('id');
echo "UID: {$uid}\n";

$svc = app('App\\Services\\LogbookAiService');

$tests = [
    [
        'user_id' => 5,
        'tanggal' => '2026-05-23',
        'kegiatan' => 'test ajah',
        'sasaran_pekerjaan' => 'RAPAT KOORDINASI',
        'lokasi' => 'Mengikuti rapat koordinasi dengan kepala sekolah',
    ],
    [
        'user_id' => 5,
        'tanggal' => '2026-05-23',
        'kegiatan' => 'asda',
        'sasaran_pekerjaan' => '2026',
        'lokasi' => 'Aula BPMP',
    ],
    [
        'user_id' => 5,
        'tanggal' => '2026-05-23',
        'kegiatan' => 'sadsa',
        'sasaran_pekerjaan' => '2026',
        'lokasi' => 'Aula BPMP',
    ],
    [
        'user_id' => 5,
        'tanggal' => '2026-05-23',
        'kegiatan' => 'Kegiatan berbeda tapi lokasi sama',
        'sasaran_pekerjaan' => 'Uraian berbeda',
        'lokasi' => 'Aula BPMP',
    ],
];

foreach ($tests as $test) {
    echo "\nTest: kegiatan='" . $test['kegiatan'] . "' sasaran='" . $test['sasaran_pekerjaan'] . "' lokasi='" . $test['lokasi'] . "'\n";
    print_r($svc->detectDuplicate($test));
    $method = new ReflectionMethod(App\Services\LogbookAiService::class, 'calculateSimilarity');
    $method->setAccessible(true);
    echo "Similarity of kegiatan to 'asda': " . $method->invoke($svc, $test['kegiatan'], 'asda') . "\n";
    echo "Similarity of kegiatan to 'sadsa': " . $method->invoke($svc, $test['kegiatan'], 'sadsa') . "\n";
}
