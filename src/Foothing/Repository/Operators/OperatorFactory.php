<?php namespace Foothing\Repository\Operators;

use Foothing\Repository\CriteriaFilter;

class OperatorFactory {
    protected static $allowedOperators = ['=', 'like', '<', '<=', '>', '>=', '!=', 'in'];

    public static function validate($operator) {
        return in_array($operator, self::$allowedOperators);
    }

    public static function make(CriteriaFilter $filter) {
        if (! self::validate($filter->operator)) {
            throw new \Exception("Unallowed operator: $filter->operator");
        }

        switch($filter->operator) {
            case 'in':
                return new InOperator($filter);
            default:
                return new DefaultOperator($filter);
        }
    }
}
