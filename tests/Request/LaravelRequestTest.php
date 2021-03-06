<?php namespace Foothing\Repository\Tests\Request;

use Foothing\Request\Laravel\RemoteQuery;

class LaravelRequestTest extends \PHPUnit_Framework_TestCase {

    public function testQuerySpawnsProperly() {
        \Mockery::mock('alias:Illuminate\Support\Facades\Log')->shouldReceive('debug');

        $qparams = [];

        // Check empty query.
        $query = $this->spawn($qparams);
        $this->assertFalse($query->sortEnabled);
        $this->assertNull($query->sortField);
        $this->assertEquals('asc', $query->sortDirection);
        $this->assertFalse($query->filterEnabled);
        $this->assertNull($query->filters);

        // No exceptions for sort command without sort field.
        $qparams = ['sort'];
        $this->spawn($qparams);

        $qparams = [ 'sort' => null ];
        $this->spawn($qparams);

        // Check sort query (default asc).
        $qparams = [ 'sort' => (object)['field' => 'name'] ];
        $query = $this->spawn($qparams);
        $this->assertTrue($query->sortEnabled);
        $this->assertNotNull($query->sortField);
        $this->assertEquals('asc', $query->sortDirection);
        $this->assertFalse($query->filterEnabled);
        $this->assertNull($query->filters);

        // Check sort direction.
        $qparams = [ 'sort' => (object)['field' => 'name', 'direction' => 'desc'] ];
        $query = $this->spawn($qparams);
        $this->assertTrue($query->sortEnabled);
        $this->assertNotNull($query->sortField);
        $this->assertEquals('desc', $query->sortDirection);
        $this->assertFalse($query->filterEnabled);
        $this->assertNull($query->filters);

        // Check filters.
        $qparams = [ 'filter' => null ];
        $this->spawn($qparams);

        $qparams = [ 'filter' => (object)[
            'fields' => [
                (object)['name' => 'email', 'value' => 'test', 'operator' => '<='],
                (object)['name' => 'country', 'value' => 'Italy', 'operator' => 'like'],
                (object)['name' => 'amount', 'value' => 0, 'operator' => '>'],
                (object)['name' => 'nullable', 'value' => 'null', 'operator' => '='],
                (object)['name' => 'wildcards', 'value' => '* foo bar*', 'operator' => 'like']
            ]
        ]];
        $query = $this->spawn($qparams);
        $this->assertFalse($query->sortEnabled);
        $this->assertNull($query->sortField);
        $this->assertEquals('asc', $query->sortDirection);
        $this->assertTrue($query->filterEnabled);
        $this->assertNotNull($query->filters);
        $this->assertEquals('email', $query->filters[0]->name);
        $this->assertEquals('test', $query->filters[0]->value);
        $this->assertEquals('<=', $query->filters[0]->operator);
        $this->assertEquals('country', $query->filters[1]->name);
        $this->assertEquals('Italy', $query->filters[1]->value);
        $this->assertEquals('like', $query->filters[1]->operator);
        $this->assertEquals('amount', $query->filters[2]->name);
        $this->assertEquals(0, $query->filters[2]->value);
        $this->assertEquals('>', $query->filters[2]->operator);
        $this->assertEquals('nullable', $query->filters[3]->name);
        $this->assertEquals(null, $query->filters[3]->value);
        $this->assertEquals('=', $query->filters[3]->operator);
        $this->assertEquals('wildcards', $query->filters[4]->name);
        $this->assertEquals("% foo bar%", $query->filters[4]->value);
        $this->assertEquals('like', $query->filters[4]->operator);
    }

    protected function spawn($qparams) {
        $qparams = json_encode( (object)$qparams );
        return RemoteQuery::spawn($qparams);
    }
}
