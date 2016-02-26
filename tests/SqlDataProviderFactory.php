<?php
namespace CrudKitTests;

use CrudKit\Data\SQLDataProvider;
use Doctrine\DBAL\DriverManager;

/**
 * Class DataProviderFactory
 *
 * Creates DataProvider instances for testing
 */
class SqlDataProviderFactory
{
    private static $defaultConnectionParams = [
        'driver'    => 'pdo_sqlite',
        'path'      => '/fixtures/chinook.sqlite',
    ];

    /**
     * @var \Doctrine\DBAL\Connection
     */
    private static $defaultConnection;

    /**
     * A default
     *
     * @var array
     */
    private static $defaultSqlProvider = [
        'table'         => 'Customer',
        'primary_col'   => 'CustomerId',
        'columns'       => [
            'FirstName' => [
                'label'     => 'First Name',
                'options'   => ['required' => true]
            ],
            'LastName'  => [
                'label'     => 'Last Name',
                'options'   => ['required' => true]
            ],
            'City'      => [
                'label'     => 'City',
                'options'   => []
            ],
            'Email'     => [
                'label'     => 'Email',
                'options'   => ['required' => true]
            ],
        ],
        'summary_cols'  => [
            'FirstName',
            'LastName',
        ],
    ];


    /**
     * @param array $params
     * @return \Doctrine\DBAL\Connection
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function connection(array $params = [])
    {
        if(!empty($params)) {
            return DriverManager::getConnection($params);
        }

        $params = static::$defaultConnectionParams;
        $params['path'] = __DIR__ . $params['path'];
        if(is_null(static::$defaultConnection)) {
            static::$defaultConnection = DriverManager::getConnection($params);
        }
        return static::$defaultConnection;
    }

    public static function sqlDataProvider($table, $primaryCol, array $cols, array $summaryCols)
    {

        $connection = static::connection();
        $provider = new SQLDataProvider($connection, $table, $primaryCol, $summaryCols);
        foreach($cols as $id => $column) {
            if(is_string($column)) {
                $label = $column;
                $options = [];
            } else {
                $label = $column['label'];
                $options = $column['options'];
            }
            $provider->addColumn($id, $id, $label, $options);
        }
        $provider->init();
        return $provider;
    }

    public static function defaultSqlDataProvider()
    {
        $table = static::$defaultSqlProvider['table'];
        $primary = static::$defaultSqlProvider['primary_col'];
        $columns = static::$defaultSqlProvider['columns'];
        $summaryCols = static::$defaultSqlProvider['summary_cols'];
        return static::sqlDataProvider($table, $primary, $columns, $summaryCols);
    }

    public static function defaultSqlDataProviderWithEmailValidator()
    {
        $table = static::$defaultSqlProvider['table'];
        $primary = static::$defaultSqlProvider['primary_col'];
        $columns = static::$defaultSqlProvider['columns'];
        $summaryCols = static::$defaultSqlProvider['summary_cols'];

        $columns['Email']['options']['validator'] = 'validate_email';
        return static::sqlDataProvider($table, $primary, $columns, $summaryCols);
    }
}
