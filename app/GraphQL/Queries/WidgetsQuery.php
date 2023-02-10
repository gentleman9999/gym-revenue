<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Services\Dashboard\HomeDashboardService;

final class WidgetsQuery
{
    protected HomeDashboardService $service;

    public function __construct(HomeDashboardService $service)
    {
        $this->service = $service;
    }

    /**
     * @param null                 $_
     * @param array<string, mixed> $_args
     *
     * @return array<array<string, mixed>>
     */
    public function __invoke($_, array $_args): array
    {
        // TODO implement the resolver
        return $this->service->getDashboardWidgets();
    }
}
