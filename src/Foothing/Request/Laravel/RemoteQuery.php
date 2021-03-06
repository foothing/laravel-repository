<?php namespace Foothing\Request\Laravel;

use Foothing\Request\AbstractRemoteQuery;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;

class RemoteQuery extends AbstractRemoteQuery {

    public static function spawn($input) {
        $input = json_decode($input);

        // @TODO security checks.
        $query = new RemoteQuery();

        if (! $input) {
            return $query;
        }

        if (property_exists($input, 'sort') && $input->sort && property_exists($input->sort, 'field')) {
            $query->sortField = $input->sort->field;
            $query->sortDirection = property_exists($input->sort, 'direction') && $input->sort->direction ? $input->sort->direction : 'asc';
            $query->sortEnabled = true;
        }

        if (property_exists($input, 'filter') && $input->filter && property_exists($input->filter, 'fields')) {

            foreach ($input->filter->fields as $field) {
                if (property_exists($field, 'name') && property_exists($field, 'value') && $field->value !== null) {
                    $query->filters[] = (object)[
                        'name' => $field->name,
                        'value' => self::parseValue($field->value),
                        'operator' => $field->operator
                    ];
                    $query->filterEnabled = true;
                }
            }
        }

        return $query;
    }

    protected static function parseValue($raw) {
        if ($raw === 'null') {
            return null;
        }
        return preg_replace("/\*/", "%", $raw);
    }
}
