<?php
$mysqli = new mysqli("localhost", "root", "", "hrweb_db");

$clientsQuery = $mysqli->query("SELECT company_name, hr_contact FROM clients")->fetch_all(MYSQLI_ASSOC);

$tsrList = [
    ['email' => 'net@hrweb.ph', 'full_name' => 'Net']
];

$kpiData = [];
foreach ($tsrList as $tsr) {
    $kpiData[$tsr['email']] = [
        'name' => $tsr['full_name'],
        'leads' => 0,
        'coleads' => 0,
        'utilization' => 0
    ];
}

$detailedAssignments = [];

foreach ($clientsQuery as $client) {
    $contacts = json_decode($client['hr_contact'], true);
    if (is_array($contacts)) {
        $lead = $contacts['lead'] ?? null;
        $co1 = $contacts['co1'] ?? null;
        $co2 = $contacts['co2'] ?? null;
        
        if ($lead || $co1 || $co2) {
            $detailedAssignments[] = [
                'company' => $client['company_name'],
                'lead' => $lead,
                'co1' => $co1,
                'co2' => $co2
            ];
        }

        if ($lead && isset($kpiData[$lead])) {
            $kpiData[$lead]['leads']++;
        }
        
        $uniqueCoLeads = array_unique(array_filter([$co1, $co2]));
        foreach ($uniqueCoLeads as $coTsr) {
            if (isset($kpiData[$coTsr])) {
                if ($coTsr !== $lead) {
                    $kpiData[$coTsr]['coleads']++;
                }
            }
        }
    }
}

$minTarget = 10;
foreach ($kpiData as $email => &$data) {
    if ($minTarget > 0) {
        $data['utilization'] = round(($data['leads'] / $minTarget) * 100);
    }
}
unset($data);

print_r($kpiData);
print_r($detailedAssignments);
