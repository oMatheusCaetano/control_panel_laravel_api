<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

trait ParameterHandler
{
    protected function getParam(
        Collection|EloquentCollection|array|null $params = [],
        string  $key,
        $default = null
    ) {
        $params = $this->paramsKeysToUpperCase($params);
        $key = strtoupper($key);
        return array_key_exists($key, $params) ? $params[$key] : $default;
    }

    protected function paramsKeysToUpperCase(Collection|EloquentCollection|array|null $params = [])
    {
        return array_change_key_case($this->handleParams($params)->toArray(), CASE_UPPER);
    }

    protected function handleParams(Collection|EloquentCollection|array|null $params = []): Collection
    {
        if (! $params
            || ($params instanceof Collection && $params->isEmpty())
            || ($params instanceof EloquentCollection && $params->isEmpty())
            || (is_array($params) && empty($params))
        ) {
            return collect([]);
        }

        return collect($params);
    }
}
