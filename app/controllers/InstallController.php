<?php
use Illuminate\Database\Capsule\Manager as Capsule;

class InstallController extends BaseController
{

    /**
     * display database configuration form
     */
    public function index()
    {
        $this->loadJs('app/install.js');
        $this->publish('baseUrl', $this->data['baseUrl']);
        App::render('install/configure_db.twig', $this->data);
    }

    /**
     * Display finish page
     */
    public function finish()
    {
        App::render('install/finish.twig', $this->data);
    }

    /**
     * check database connection based on provided setting
     */
    public function checkConnection()
    {
        $success = false;
        $message = '';
        $config  = $this->getPostConfiguration();

        try{
            $this->makeConnection($config);

            /**
             * Just trying to show tables with current connection
             */
            switch ($config['driver']) {
                case 'mysql':
                    $tables = Capsule::select('show tables');
                    break;

                case 'sqlite':
                    $tables = Capsule::select("SELECT * FROM sqlite_master WHERE type='table'");
                    break;

                case 'sqlsrv':
                    $tables = Capsule::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'");
                    break;

                case 'pgsql':
                    $tables = Capsule::select("SELECT * FROM pg_catalog.pg_tables");
                    break;
            }


            $success = true;
            $message = 'Successfully connected!';

        }catch(Exception $e){
            $success = false;
            $message = $e->getMessage();
        }

        Response::headers()->set('Content-Type', 'application/json');
        Response::setBody(json_encode(
            array(
                'success' => $success,
                'message' => $message,
                'config'  => $config
            )
        ));
    }

    /**
     * write configuration to database configuration
     */
    public function writeConfiguration()
    {
        $configFile= APP_PATH.'config/database.php';
        $config    = $this->getPostConfiguration();

        $configStr = <<<CONFIG
<?php

\$config['database'] = array(
    'default'       => '{$config['driver']}',

    'connections'   => array(
        '{$config['driver']}'     => array(
            'driver'    => '{$config['driver']}',
            'host'      => isset(\$_SERVER['DB1_HOST']) ? \$_SERVER['DB1_HOST'] : '{$config['host']}',
            'database'  => isset(\$_SERVER['DB1_NAME']) ? \$_SERVER['DB1_NAME'] : '{$config['database']}',
            'username'  => isset(\$_SERVER['DB1_USER']) ? \$_SERVER['DB1_USER'] : '{$config['username']}',
            'password'  => isset(\$_SERVER['DB1_PASS']) ? \$_SERVER['DB1_PASS'] : '{$config['password']}',
            'charset'   => '{$config['charset']}',
            'collation' => '{$config['collation']}',
            'prefix'    => '{$config['prefix']}'
        )
    )
);
CONFIG;

        file_put_contents($configFile, $configStr);

        $this->makeConnection($config);
        $this->migrate();
        $this->seed();

        Response::redirect($this->data['baseUrl'].'install.php/finish');
    }

    /**
     * Get configuration value posted by user
     */
    private function getPostConfiguration()
    {
        $driver     = Input::post('dbdriver');
        $database   = Input::post('dbname');

        /**
         * point to APP_PATH/storage/db if driver is sqlite
         */
        if($driver == 'sqlite'){
            $database = APP_PATH.'storage/db/'.$database.'.sqlite';
        }

        return array(
            'driver'    => $driver,
            'host'      => Input::post('dbhost'),
            'database'  => $database,
            'username'  => Input::post('dbuser'),
            'password'  => Input::post('dbpass'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        );
    }

    /**
     * create connection to the database based on given configuration
     */
    private function makeConnection($config)
    {
        try{
            $capsule = new Capsule;

            $capsule->addConnection($config);
            $capsule->setAsGlobal();
            $capsule->bootEloquent();

        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * migrate the database schema
     */
    private function migrate()
    {
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
    private function seed()
    {
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
        }catch(\Exception $e){
            App::flash('message', $e->getMessage());
        }
    }
}