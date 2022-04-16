<?php

namespace Tests\Feature;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\Traits\PaginatorTester;
use Tests\TestCase;

abstract class ListTestClass extends TestCase
{
    use PaginatorTester;

    protected string $modelClass;
    protected string $endpoint;
    protected array $integerFields = ['id'];
    protected array $stringFields = [];

    public function test_list_all_items_as_json_with_status_200()
    {
        resolve($this->modelClass)::truncate();
        resolve($this->modelClass)::factory(20)->create();
        $expectedJson = resolve($this->modelClass)::all()->toJson();

        $response = $this->get($this->endpoint);

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/json');
        $this->assertEquals($expectedJson, json_encode($response->json()));
    }

    public function test_list_all_items_as_json_with_status_200_and_pagination()
    {
        //? Arrange
        resolve($this->modelClass)::truncate();
        resolve($this->modelClass)::factory(20)->create();
        $reqUri = $this->endpoint . $this->prepareQueryParams();
        $url = $this->prepareUrlForRequest($reqUri);

        $expectedData = resolve($this->modelClass)::limit(10)->get()->toArray();
        $expectedLinks = [
            [
                'url' => null,
                'label' => '&laquo; Previous',
                'active' => false
            ],
            [
                'url' => $url . '&__page=1',
                'label' => '1',
                'active' => true
            ],
            [
                'url' => $url . '&__page=2',
                'label' => '2',
                'active' => false
            ],
            [
                'url' => $url . '&__page=2',
                'label' => 'Next &raquo;',
                'active' => false
            ],
        ];

        //? Act
        $response = $this->get($reqUri);

        //? Assert
        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertJson(fn (AssertableJson $json) => $json
            ->where('current_page', 1)
            ->where('data', $expectedData)
            ->where('first_page_url',  $url . '&__page=1')
            ->where('from', 1)
            ->where('last_page', 2)
            ->where('last_page_url', $url . '&__page=2')
            ->where('links', $expectedLinks)
            ->where('next_page_url', $url . '&__page=2')
            ->where('path', explode('?', $url)[0])
            ->where('per_page', 10)
            ->where('to', 10)
            ->where('total', 20)
            ->where('prev_page_url', null)
        );
    }

    public function test_list_all_items_as_json_with_status_200_and_pagination_and_pagination_params()
    {
        //? Arrange
        resolve($this->modelClass)::truncate();
        resolve($this->modelClass)::factory(20)->create();
        $reqUri = $this->endpoint . $this->prepareQueryParams();
        $url = $this->prepareUrlForRequest($reqUri);

        $expectedData = resolve($this->modelClass)::whereIn('id', [11, 12, 13, 14, 15])->get()->toArray();
        $expectedLinks = [
            [
                'url' => $url . '&__per_page=5&__page=2',
                'label' => '&laquo; Previous',
                'active' => false
            ],
            [
                'url' => $url . '&__per_page=5&__page=1',
                'label' => '1',
                'active' => false
            ],
            [
                'url' => $url . '&__per_page=5&__page=2',
                'label' => '2',
                'active' => false
            ],
            [
                'url' => $url . '&__per_page=5&__page=3',
                'label' => '3',
                'active' => true
            ],
            [
                'url' => $url . '&__per_page=5&__page=4',
                'label' => '4',
                'active' => false
            ],
            [
                'url' => $url . '&__per_page=5&__page=4',
                'label' => 'Next &raquo;',
                'active' => false
            ],
        ];

        //? Act
        $response = $this->get($reqUri . '&__per_page=5&__page=3');

        //? Assert
        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertJson(fn (AssertableJson $json) => $json
            ->where('current_page', 3)
            ->where('data', $expectedData)
            ->where('first_page_url',  $url . '&__per_page=5&__page=1')
            ->where('from', 11)
            ->where('last_page', 4)
            ->where('last_page_url', $url . '&__per_page=5&__page=4')
            ->where('links', $expectedLinks)
            ->where('next_page_url', $url . '&__per_page=5&__page=4')
            ->where('path', explode('?', $url)[0])
            ->where('per_page', 5)
            ->where('to', 15)
            ->where('total', 20)
            ->where('prev_page_url', $url . '&__per_page=5&__page=2')
        );
    }

    public function test_filter_by_integer_fields()
    {
        $model = $this->getModel();
        foreach ($this->integerFields as $column) {
            $model::truncate();
            $model::factory(1)->create([$column => 1]);
            $model::factory(1)->create([$column => 2]);
            $model::factory(1)->create([$column => 3]);
            $model::factory(1)->create([$column => 4]);
            $this->makeTestToFilterByColumn($column, 3, ['=', '']);
        }
    }


    public function test_filter_by_string_fields()
    {
        foreach ($this->stringFields as $column) {
            $this->getModel()::truncate();
            $this->getModel()::factory(1)->create([$column => 'A']);
            $this->getModel()::factory(1)->create([$column => 'B']);
            $this->getModel()::factory(1)->create([$column => 'C']);
            $this->getModel()::factory(1)->create([$column => 'D']);
            $this->makeTestToFilterByColumn($column, 'B', ['=', '']);
        }
    }

    protected function makeTestToFilterByColumn($column, $value, $operators)
    {
        $query = resolve($this->modelClass)::where($column, $operators[0], $value);
        $queryLog = (clone $query)->toSql();
        $data = $query->get();
        $expectedJson = $data->toJson();
        $uri = $this->endpoint . '?' . $this->randomUpperCase($column) . $operators[1] . '=' . $value;
        $response = $this->get($uri);
        try {
            $response->assertOk();
            $response->assertHeader('Content-Type', 'application/json');
            $this->assertEquals($expectedJson, json_encode($response->json()));
        } catch (Exception $e) {
            dump([
                'column' => $column,
                'value' => $value,
                'operators' => $operators,
                'query' => $queryLog,
                'data' => $data->toArray(),
                'expected' => $expectedJson,
            ]);

            throw $e;
        }
    }

    protected function randomUpperCase(string $string)
    {
        $characters = str_split($string);
        $i = 0;
        do{
            $random_index = rand(0, count($characters) - 1);
            $unique_indices[] = ""; //UNIQUE INDICES
            while (in_array($random_index, $unique_indices)) {
                $random_index = rand(0, count($characters) - 1);
            }
            $unique_indices[] = $random_index;

            $random_letter = $characters[$random_index];
            if(ctype_alpha($random_letter)){//only letters
                $characters[$random_index] = strtoupper($random_letter);
                $i++;
            }
        }while($i < count($characters));

        return implode('', $characters);
    }

    protected function getModel()
    {
        return resolve($this->modelClass);
    }
}
