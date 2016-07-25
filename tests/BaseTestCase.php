<?php namespace Foothing\Repository\Tests;

class BaseTestCase extends \Orchestra\Testbench\TestCase {

    protected function getEnvironmentSetUp($app) {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   	=> 'mysql',
            'host' 		=> 'localhost',
            'database' 	=> 'lcommon',
            'username'	=> 'lcommon',
            'password'	=> 'lcommon',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ]);

        $app['config']->set('mail.pretend', true);
    }

    public function setUp() {
        parent::setUp();
        $this->artisan('migrate', [
            '--database'	=>	'testbench',
            '--realpath'	=> 	realpath(__DIR__ . '/database'),
        ]);

        // @TODO refactor fixtures, this schema sucks so much :(

        \DB::table('person')->delete();
        \DB::table('son')->delete();
        \DB::table('roles')->delete();

        \DB::table('person')->insert(['id' => 1, 'name' => 'Homer']);
        \DB::table('person')->insert(['id' => 2, 'name' => 'Marge']);
        \DB::table('person')->insert(['id' => 3, 'name' => 'Apu']);

        \DB::table('son')->insert(['pid' => 1, 'name' => 'Bart']);
        \DB::table('son')->insert(['pid' => 1, 'name' => 'Lisa']);
        \DB::table('son')->insert(['pid' => 1, 'name' => 'Maggie']);
        \DB::table('son')->insert(['pid' => 2, 'name' => 'Lisa']);

        \DB::table('roles')->insert(['id' => 1, 'name' => 'Father']);
        \DB::table('roles')->insert(['id' => 2, 'name' => 'Mother']);
        \DB::table('roles')->insert(['id' => 3, 'name' => 'Vendor']);
        \DB::table('roles')->insert(['id' => 4, 'name' => 'Children']);
        \DB::table('roles')->insert(['id' => 5, 'name' => 'Nuclear Safety Inspector']);
        \DB::table('roles')->insert(['id' => 6, 'name' => 'Ambulance Driver']);
        \DB::table('roles')->insert(['id' => 7, 'name' => 'Food Critic']);

        \DB::table('person_roles')->insert(['pid' => 1, 'rid' => 1]);
        \DB::table('person_roles')->insert(['pid' => 2, 'rid' => 2]);
        \DB::table('person_roles')->insert(['pid' => 3, 'rid' => 3]);
    }

    public function tearDown() {
        \Mockery::close();
    }

    public function testNoWarning() { }
}