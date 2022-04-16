<?php

namespace Tests\Feature\Traits;

trait PaginatorTester
{
    protected function prepareQueryParams($perPage = null, $page = null, string $queryParams = '')
    {
        $query = '?__data_type=paginated';

        if ($perPage) {
            $query .= '&__per_page=' . $perPage;
        }

        if ($page) {
            $query .= '&__page=' . $page;
        }

        if (strlen($queryParams)) {
            $query .= '&' . $queryParams;
        }

        return $query;
    }
}
