<?php
namespace CrudKitTests;

use CrudKit\Data\ArrayDataProvider;

class ArrayDataProviderFactory
{

    private static $defaultArrayProvider = [
        'schema'    => [
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
        'summary_cols' => [
            'FirstName',
            'LastName'
        ]
    ];


    public static function arrayDataProvider(array $schema, array $summaryCols, array $data = [])
    {
        return new ArrayDataProvider($schema, $summaryCols, $data);
    }

    public static function defaultArrayDataProviderWithEmailValidator()
    {
        $schema = static::$defaultArrayProvider['schema'];
        $schema['Email']['options']['validator'] = 'validate_email';
        $data = static::generateDataForArrayProvider(0);
        return static::arrayDataProvider($schema, static::$defaultArrayProvider['summary_cols'], $data);
    }

    public static function defaultArrayDataProvider($numDataRows = 10)
    {
        $data = static::generateDataForArrayProvider($numDataRows);
        return static::arrayDataProvider(
            static::$defaultArrayProvider['schema'],
            static::$defaultArrayProvider['summary_cols'],
            $data
        );
    }

    public static function generateDataForArrayProvider($numDataRows)
    {
        $data = [];
        foreach( range(0, ((int) $numDataRows - 1) ) as $rowNumber) {
            $data[] = [
                'FirstName' => 'First Name #' . $rowNumber,
                'LastName'  => 'Last Name #' . $rowNumber,
                'City'      => 'City #' . $rowNumber,
                'Email'     => 'email' . str_pad($rowNumber, 3, STR_PAD_LEFT) . '@example.com'
            ];
        }
        return $data;
    }
}
