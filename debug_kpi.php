<?php
require 'system/bootstrap.php';
require 'public/index.php';

$controller = current_user() ?? null; // not in web context, manual override
$db = \Config\Database::connect();

$builder = $db->table('users');
$builder->select('users.id, users.email, users.status, tsrs.full_name, tsrs.employee_id');
$builder->join('tsrs', 'tsrs.user_id = users.id');
$builder->where('users.role', 'tsr');
$tsrList = $builder->get()->getResultArray();

$minTargetQuery = $db->table('system_settings')->where('setting_key', 'min_tsr_leads')->get()->getRow();
$minTarget = $minTargetQuery ? (int)$minTargetQuery->setting_value : 10;

$clientsQuery = $db->table('clients')->select('company_name, hr_contact')->get()->getResultArray();

$detailedAssignments = [];
$kpiData = [];

foreach ($tsrList as $tsr) {
    $kpiData[$tsr['email']] = [
        'name' => $tsr['full_name'],
        'leads' => 0,
        'coleads' => 0,
        'utilization' => 0
    ];
}

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

foreach ($kpiData as $email => &$data) {
    if ($minTarget > 0) {
        $data['utilization'] = round(($data['leads'] / $minTarget) * 100);
    }
}
unset($data);

print_r($kpiData);
print_r($detailedAssignments);
