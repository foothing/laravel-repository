<?php namespace Foothing\Repository\Operators;

use Foothing\Repository\CriteriaFilter;

abstract class AbstractOperator {
    /**
     * @var \Foothing\Repository\CriteriaFilter
     */
    protected $filter;

    /**
     * @var string
     */
    protected $field;

    public function __construct(CriteriaFilter $filter) {
        $this->filter = $filter;
        $this->field = $this->guessField($filter->field);
    }

    /**
     * Where the actual query criteria gets applied.
     *
     * @param $query
     *
     * @return mixed
     */
    public abstract function apply($query);

    /**
     * Guess the criteria field name. If $field is a string, it
     * is returned as-is. If it contains "." this method will
     * try to split in "relation.field" format and return
     * a NestedField object. Any other case will raise an
     * Exception.
     *
     * @param string $field
     *  The criteria->field.
     *
     * @return NestedField|string
     * @throws \Exception
     */
    public function guessField($field) {
        // Explode by ".", only two allowed kind of values:
        // - a string like "name"
        // - a string like "relation.name"
        $chunks = explode(".", $field);
        $count = count($chunks);

        // Chunk 0 is empty, this is a malformed input.
        if ($count == 1 && ! $chunks[0]) {
            throw new \Exception("Field is empty.");
        }

        // Malformed input again, probably in the form ".foo".
        elseif ($count == 2 && (! $chunks[0] || !$chunks[1])) {
            throw new \Exception("Field is empty or broken.");
        }

        // Only one chunk: assume field is an actual table column.
        elseif ($count == 1) {
            return $field;
        }

        // Two chunks: assume this is a nested field.
        elseif ($count == 2) {
            return new NestedField($chunks[0], $chunks[1]);
        }

        else {
            throw new \Exception("Field has a bad format (3 or more tokens).");
        }
    }
}
