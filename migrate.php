<?php

define('APP_PATH'   , __DIR__.'/app/');

$config = array();
require __DIR__.'/vendor/autoload.php';
require __DIR__.'/app/config/database.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Cartalyst\Sentry\Facades\Native\Sentry;

class Migrator{

    protected $config;

    public function __construct($config){
        $this->config = $config;
        $this->makeConnection($config);
    }

    /**
     * create connection to the database based on given configuration
     */
    private function makeConnection($config){
        try{
            $capsule = new Capsule;

            $capsule->addConnection($config);
            $capsule->setAsGlobal();
            $capsule->bootEloquent();

            Sentry::setupDatabaseResolver($capsule->connection()->getPdo());

        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * migrate the database schema
     */
    public function migrate(){
        /**
         * create table for sentry user
         */
        if (!Capsule::schema()->hasTable('users')){
            Capsule::schema()->create('users', function($table)
            {
                $table->increments('id');
                $table->string('email');
                $table->string('password');
                $table->text('permissions')->nullable();
                $table->boolean('activated')->default(0);
                $table->string('activation_code')->nullable();
                $table->timestamp('activated_at')->nullable();
                $table->timestamp('last_login')->nullable();
                $table->string('persist_code')->nullable();
                $table->string('reset_password_code')->nullable();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->timestamps();

                // We'll need to ensure that MySQL uses the InnoDB engine to
                // support the indexes, other engines aren't affected.
                $table->engine = 'InnoDB';
                $table->unique('email');
                $table->index('activation_code');
                $table->index('reset_password_code');
            });
        }

        /**
         * create table for sentry group
         */
        if (!Capsule::schema()->hasTable('groups')){
            Capsule::schema()->create('groups', function($table)
            {
                $table->increments('id');
                $table->string('name');
                $table->text('permissions')->nullable();
                $table->timestamps();

                // We'll need to ensure that MySQL uses the InnoDB engine to
                // support the indexes, other engines aren't affected.
                $table->engine = 'InnoDB';
                $table->unique('name');
            });
        }

        /**
         * create user-group relation
         */
        if (!Capsule::schema()->hasTable('users_groups')){
            Capsule::schema()->create('users_groups', function($table)
            {
                $table->integer('user_id')->unsigned();
                $table->integer('group_id')->unsigned();

                // We'll need to ensure that MySQL uses the InnoDB engine to
                // support the indexes, other engines aren't affected.
                $table->engine = 'InnoDB';
                $table->primary(array('user_id', 'group_id'));
            });
        }

        /**
         * create throttle table
         */
        if (!Capsule::schema()->hasTable('throttle')){
            Capsule::schema()->create('throttle', function($table)
            {
                $table->increments('id');
                $table->integer('user_id')->unsigned();
                $table->string('ip_address')->nullable();
                $table->integer('attempts')->default(0);
                $table->boolean('suspended')->default(0);
                $table->boolean('banned')->default(0);
                $table->timestamp('last_attempt_at')->nullable();
                $table->timestamp('suspended_at')->nullable();
                $table->timestamp('banned_at')->nullable();

                // We'll need to ensure that MySQL uses the InnoDB engine to
                // support the indexes, other engines aren't affected.
                $table->engine = 'InnoDB';
                $table->index('user_id');
            });
        }
    }

    /**
     * seed the database with initial value
     */
    public function seed(){
        try{
            Sentry::createUser(array(
                'email'       => 'admin@admin.com',
                'password'    => 'password',
                'first_name'  => 'Website',
                'last_name'   => 'Administrator',
                'activated'   => 1,
                'permissions' => array(
                    'admin'     => 1
                )
            ));
        }catch(Exception $e){
            echo $e->getMessage()."\n";
        }
    }
}

$migrator = new Migrator($config['database']['connections'][$config['database']['default']]);

$migrator->migrate();
$migrator->seed();
?>