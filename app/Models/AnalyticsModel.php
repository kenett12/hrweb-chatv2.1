<?php

namespace App\Models;

use CodeIgniter\Model;

class AnalyticsModel extends Model
{
    protected $table            = 'analytics';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['client_id', 'type', 'ip_address', 'session_id', 'created_at'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    /**
     * getDailyStats
     * Fetches counts for today and compares with the daily average of the last 7 days.
     */
    public function getAnalyticsStats($clientId)
    {
        $today = date('Y-m-d');
        $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));

        // 1. Visitors Today (Unique Session IDs)
        $visitorsToday = $this->where('client_id', $clientId)
                             ->where('type', 'visitor')
                             ->where('created_at >=', $today . ' 00:00:00')
                             ->countAllResults();

        // 2. Page Views Today
        $viewsToday = $this->where('client_id', $clientId)
                           ->where('type', 'page_view')
                           ->where('created_at >=', $today . ' 00:00:00')
                           ->countAllResults();

        // 3. Last 7 Days Avg (excluding today to show trend)
        $last7DaysStart = date('Y-m-d', strtotime('-8 days'));
        $last7DaysEnd = date('Y-m-d', strtotime('-1 days'));

        $visitorsLast7 = $this->where('client_id', $clientId)
                              ->where('type', 'visitor')
                              ->where('created_at >=', $last7DaysStart . ' 00:00:00')
                              ->where('created_at <=', $last7DaysEnd . ' 23:59:59')
                              ->countAllResults() / 7;

        $viewsLast7 = $this->where('client_id', $clientId)
                            ->where('type', 'page_view')
                            ->where('created_at >=', $last7DaysStart . ' 00:00:00')
                            ->where('created_at <=', $last7DaysEnd . ' 23:59:59')
                            ->countAllResults() / 7;

        return [
            'visitors_today'     => $visitorsToday,
            'views_today'        => $viewsToday,
            'visitors_trend'     => $visitorsLast7 > 0 ? (($visitorsToday - $visitorsLast7) / $visitorsLast7) * 100 : 0,
            'views_trend'        => $viewsLast7 > 0 ? (($viewsToday - $viewsLast7) / $viewsLast7) * 100 : 0,
            'visitors_last_7'    => round($visitorsLast7, 1),
            'views_last_7'       => round($viewsLast7, 1),
        ];
    }
}
