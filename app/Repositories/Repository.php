<?php

namespace App\Repositories;

use App\Exceptions\NoModelClassDefinedException;
use App\Exceptions\UnableToResolveModelClassException;
use App\Models\BaseModel;
use App\Repositories\Contracts\IRepository;
use App\Traits\ParameterHandler;
use App\Utils\Date;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class Repository implements IRepository
{
    use ParameterHandler;

    protected $modelClass = null;
    protected ?Builder $query = null;
    protected ?BaseModel $model = null;
    protected array $tableFields = [];

    public function __construct()
    {
        if (! is_string($this->modelClass)) {
            throw new NoModelClassDefinedException();
        }

        $model = resolve($this->modelClass);

        if (! $model instanceof BaseModel) {
            throw new UnableToResolveModelClassException();
        }

        $this->model = $model;
        $this->query = $this->model->query();
        $fields = Schema::getColumnListing($this->model->getTable());
        foreach ($fields as $field) {
            $this->tableFields[strtoupper($field)] = $field;
        }
    }

    public function get(
        Collection|EloquentCollection|array|null $params = []
    ): EloquentCollection | LengthAwarePaginator {
        $params = $this->handleParams($params);
        $this->handleQueryParams($params);

        $data = collect([]);

        switch (strtoupper($this->getParam($params, '__data_type'))) {
            case 'PAGINATED':
                /** @var LengthAwarePaginator */
                $data = $this->query->paginate(
                    $this->getParam($params, '__per_page', 10),
                    ['*'],
                    '__page',
                    $this->getParam($params, '__page', 1)
                );
                $params->each(function ($value, $key) use ($data) {
                    $data->appends($key, $value);
                });
                break;

            default:
                $data = $this->query->get();
                break;
        }

        return $data;
    }

    protected function handleQueryParams(Collection $params): self
    {
        return $this
            ->handleFieldsFilters($params)
            ->handleDateTimeFieldsFilters($params);
    }

    protected function handleFieldsFilters(Collection $params): self
    {
        foreach ($params->toArray() as $key => $value) {
            $key = strtoupper((string) $key);
            $operator = '=';
            $values = explode(',', $value);

            if (str_ends_with($key, '!')) {
                $operator = '!=';
                $key = substr($key, 0, -1);
            }

            if (str_ends_with($key, '>')) {
                $operator = '>';
                $key = substr($key, 0, -1);
            }

            if (str_ends_with($key, '<')) {
                $operator = '<';
                $key = substr($key, 0, -1);
            }

            if (str_ends_with($key, '<~')) {
                $operator = '<=';
                $key = substr($key, 0, -2);
            }

            if (str_ends_with($key, '>~')) {
                $operator = '>=';
                $key = substr($key, 0, -2);
            }

            if (! isset($this->tableFields[$key])
                || in_array($this->tableFields[$key], $this->model->dateTimeFields)
            ) {
                continue;
            }

            switch ($operator) {
                case '=':
                    $this->query->whereIn($this->tableFields[$key], $values);
                    break;

                case '!=':
                    $this->query->whereNotIn($this->tableFields[$key], $values);
                    break;

                case '>':
                case '>=':
                case '<':
                case '<=':
                    foreach ($values as $itemV) {
                        $this->query->where($this->tableFields[$key], $operator, $itemV);
                    }
                    break;
            }
        }

        return $this;
    }

    protected function handleDateTimeFieldsFilters(Collection $params)
    {
        foreach ($params->toArray() as $key => $value) {
            $key = strtoupper((string) $key);

            if (! $value
                || ! isset($this->tableFields[$key])
                || ! in_array($this->tableFields[$key], $this->model->dateTimeFields)
            ) {
                continue;
            }

            $values = explode('to', $value);
            $initialDate = $values[0];
            $finalDate = '';

            if (count($values) > 1) {
                $finalDate = $values[1];
            }

            if ($initialDate && strlen($initialDate)) {
                $initialDate = Date::enWithTime($initialDate);
                dd($initialDate);
                $this->query->where($this->tableFields[$key], $initialDate);
            }
        }

        return $this;
    }
}
