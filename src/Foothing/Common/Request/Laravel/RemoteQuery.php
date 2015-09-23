<?php namespace Foothing\Common\Request\Laravel;

use Foothing\Common\Request\AbstractRemoteQuery;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;

class RemoteQuery extends AbstractRemoteQuery {

	public static function spawn($input) {
		$input = json_decode( $input );

		// @TODO security checks.
		$query = new RemoteQuery();

		if ( ! $input ) {
			Log::debug("Remote query skipping because of empty input");
			return $query;
		}

		if ( property_exists($input, 'sort') && $input->sort && property_exists($input->sort, 'field') ) {
			Log::debug("Remote query applying sort criteria");
			$query->sortField = $input->sort->field;
			$query->sortDirection = property_exists($input->sort, 'direction') && $input->sort->direction ? $input->sort->direction : 'asc';
			$query->sortEnabled = true;
		}

		if ( property_exists($input, 'filter') && $input->filter && property_exists($input->filter, 'fields') ) {

			foreach ($input->filter->fields as $field) {
				if ( property_exists($field, 'name') && property_exists($field, 'value') && $field->value ) {
					Log::debug("Remote query applying filter " . $field->name);
					$query->filters[] = (object)array(
						'name' => $field->name,
						'value' => $field->value,
						'operator' => $field->operator
					);
					$query->filterEnabled = true;
				}
			}
		}

		return $query;
	}
}