<?php

namespace Tests;

use Cbwar\Laravel\ModelChanges\Change;
use Cbwar\Laravel\ModelChanges\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase;
use Tests\Stubs\Data;
use Tests\Stubs\DataSoft;


class TrackingChangesTest extends TestCase
{

    private $tablename = "data";


    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']['modelchanges'] = require __DIR__ . '/../config/modelchanges.php';
    }


    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }


    protected function setUp()
    {
        parent::setUp();

        // Test table
        Schema::create($this->tablename, function (Blueprint $table) {
            $table->string('tracked1', 255);
            $table->integer('tracked2', false, true);
            $table->string('untracked', 255)->default('');
            $table->timestamps();
        });

        // Soft delete table
        Schema::create($this->tablename . '_softs', function (Blueprint $table) {
            $table->string('tracked1', 255);
            $table->integer('tracked2', false, true);
            $table->string('untracked', 255)->default('');
            $table->softDeletes();
            $table->timestamps();
        });

        $this->artisan('migrate', ['--database' => 'testbench']);
        Model::unguard();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }


    /**
     * @test
     */
    public function it_must_save_inserts_into_change_table()
    {
        $data = Data::create(['tracked1' => 'coucou', 'tracked2' => 2]);
        $changes = Change::all();
        $this->assertCount(1, $changes);
        /** @var Change $insert */
        $insert = $changes[0];
        $this->assertEquals('add', $insert->type);
        $this->assertEquals('coucou', $insert->ref_title);
        $this->assertEquals($data->id, $insert->ref_id);
        $this->assertEquals(Data::class, $insert->ref_model);
        $this->assertNull($insert->user_id);
    }

    /**
     * @test
     */
    public function it_must_save_inserts_into_change_table_with_logged_user()
    {
        $this->markTestIncomplete();
    }

    /**
     * @test
     */
    public function it_must_save_updates_into_change_table_with_logged_user()
    {
        $this->markTestIncomplete();
    }

    /**
     * @test
     */
    public function it_must_save_deletion_into_change_table_with_logged_user()
    {
        $this->markTestIncomplete();
    }

    /**
     * @test
     */
    public function it_must_save_updates_into_change_table()
    {
        $data = Data::create(['tracked1' => 'coucou', 'tracked2' => 2]);
        $data->tracked1 = 'hello';
        $data->save();
        $changes = Change::all();
        $this->assertCount(2, $changes);

        $update = $changes[1];
        $this->assertEquals('edit', $update->type);
        $this->assertEquals('hello', $update->ref_title);
        $this->assertEquals($data->id, $update->ref_id);
        $this->assertEquals(Data::class, $update->ref_model);
        $this->assertNull($update->user_id);
    }

    /**
     * @test
     */
    public function it_must_not_save_updates_with_untracked_fields_into_change_table()
    {
        $data = Data::create(['tracked1' => 'coucou', 'tracked2' => 2]);
        $data->untracked = 'test';
        $data->save();
        $data->tracked1 = 'test';
        $data->save();
        $changes = Change::all();
        $this->assertCount(2, $changes);
    }

    /**
     * @test
     */
    public function it_must_delete_rows_on_deletion_into_change_table()
    {
        $data = Data::create(['tracked1' => 'coucou', 'tracked2' => 2]);
        $data->tracked1 = 'hello';
        $data->save();
        $data->delete();

        $changes = Change::all();
        $this->assertCount(0, $changes);
    }

    /**
     * @test
     */
    public function it_must_keep_rows_on_deletion_into_change_table()
    {
        $this->app['config']->set('modelchanges.keep_deleted_items_changes', true);

        $data = Data::create(['tracked1' => 'coucou', 'tracked2' => 2]);
        $data->tracked1 = 'hello';
        $data->save();
        $data->delete();


        $changes = Change::all();
        $this->assertCount(3, $changes);
    }

    /**
     * @test
     */
    public function it_must_save_deletion_with_soft_deletes_into_change_table()
    {
        $data = DataSoft::create(['tracked1' => 'coucou', 'tracked2' => 2]);
        $data->tracked1 = 'hello';
        $data->save();
        $data->delete();

        $changes = Change::all();
        $this->assertCount(3, $changes);

        $delete = $changes[2];
        $this->assertEquals('delete', $delete->type);
        $this->assertEquals('hello', $delete->ref_title);
        $this->assertEquals($data->id, $delete->ref_id);
        $this->assertEquals(DataSoft::class, $delete->ref_model);
        $this->assertNull($delete->user_id);
    }


}