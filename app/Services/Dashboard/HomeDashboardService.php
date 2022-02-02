<?php

namespace App\Services\Dashboard;

use App\Actions\Dashboard\Home\GetDashboardWidgets;
use App\Models\Utility\AppState;

class HomeDashboardService
{
    public function __construct()
    {

    }

    public function getDashboardWidgets() : array
    {
        return GetDashboardWidgets::run();
    }

    public function getAppStateAnnouncements() : array
    {
        $results = [];

        $log = AppState::whereSlug('deployment-log-record')
            ->orderBy('value', 'DESC')->first();

        $datetime1 = date_create($log->value);
        $datetime2 = date_create(date('Y-m-d'));

        // Calculates the difference between DateTime objects
        $interval = date_diff($datetime1, $datetime2);
        if(intVal($interval->format('%R%a')) <= 5) {
            $misc = $log->misc;
            $misc['type'] = 'deployment-announcement';
            $results = [$misc];
        }

        return $results;
    }
}
