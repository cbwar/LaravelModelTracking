<?php

namespace Tests;

use Carbon\Carbon;
use Cbwar\Laravel\ModelChanges\Models\Change;
use Cbwar\Laravel\ModelChanges\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase;
use Tests\Stubs\Data;
use Tests\Stubs\DataSoft;

class TrackingChangesTest extends TestCase
{
    private $tablename = 'data';

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
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
            $table->dateTime('tracked3')->default(Carbon::now());
            $table->string('untracked', 255)->default('');
            $table->timestamps();
        });

        // Soft delete table
        Schema::create($this->tablename . '_softs', function (Blueprint $table) {
            $table->string('tracked1', 255);
            $table->integer('tracked2', false, true);
            $table->dateTime('tracked3')->default(Carbon::now());
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
        $this->assertSame('add', $insert->type);
        $this->assertSame('coucou', $insert->ref_title);
        $this->assertSame($data->id, (int) $insert->ref_id);
        $this->assertSame(Data::class, $insert->ref_model);
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
        $data = Data::create(['tracked1' => 'coucou', 'tracked2' => 2, 'tracked3' => Carbon::now()]);
        $data->tracked1 = 'hello';
        $data->tracked2 = '4';
        $data->tracked3 = Carbon::createFromDate(2014, 5, 5);
        $data->save();
        $changes = Change::all();
        $this->assertCount(2, $changes);

        $update = $changes[1];
        $this->assertSame('edit', $update->type);
        $this->assertSame('hello', $update->ref_title);
        $this->assertSame($data->id, (int) $update->ref_id);
        $this->assertSame(Data::class, $update->ref_model);
        $this->assertNull($update->user_id);

        $this->assertContains('<div class="tracks-field">tracked1</div>', $update->description);
        $this->assertContains('<div class="tracks-field">tracked2</div>', $update->description);
        $this->assertContains('<div class="tracks-field">tracked3</div>', $update->description);
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
    public function it_must_show_changed_fields_in_description()
    {
        $data = Data::create(['tracked1' => 'coucou', 'tracked2' => 2]);
        $data->tracked1 = 'hello';
        $data->save();

        $description = Change::find(2)->description;
        $this->assertContains('<div class="tracks-field">tracked1</div>', $description);
    }

    /**
     * @test
     */
    public function it_must_not_show_unchanged_fields_in_description()
    {
        $data = Data::create(['tracked1' => 'coucou', 'tracked2' => 2, 'tracked3' => Carbon::now()]);
        $data->tracked1 = 'salut';
        $data->tracked2 = '2';
        $data->save();

        $description = Change::find(2)->description;
        $this->assertNotContains('<div class="tracks-field">tracked2</div>', $description);
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
        $this->assertSame('delete', $delete->type);
        $this->assertSame('hello', $delete->ref_title);
        $this->assertSame($data->id, (int) $delete->ref_id);
        $this->assertSame(DataSoft::class, $delete->ref_model);
        $this->assertNull($delete->user_id);
    }
}
