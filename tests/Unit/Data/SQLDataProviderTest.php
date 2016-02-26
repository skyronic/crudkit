<?php
namespace CrudKitTests\Unit\Data;

use CrudKitTests\Unit\CrudKitTest;
use CrudKitTests\SqlDataProviderFactory;

class SQLDataProviderTest extends CrudKitTest
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    public function setUp()
    {
        parent::setUp();
        $this->connection = SqlDataProviderFactory::connection();
        $this->connection->beginTransaction();
    }

    public function jsonFilterData()
    {
        return [
            'summary' => [
                [ 'filters_json' => '[{"id":"_ck_all_summary","type":"like","value":"Ro"}]' ],
                3,
                'Roberto'
            ],
            'first name' => [
                [ 'filters_json' => '[{"id":"FirstName","type":"like","value":"Ed"}]' ],
                2,
                'Eduardo'
            ],
            'last name' => [
                [ 'filters_json' => '[{"id":"LastName","type":"like","value":"Go"}]' ],
                3,
                'Luís'
            ]
        ];
    }


    /** @test */
    public function it_retrieves_a_schema_based_on_defined_columns()
    {
        $provider = SqlDataProviderFactory::defaultSqlDataProvider();
        $schema = $provider->getSchema();

        $this->assertArrayHasKey('CustomerId', $schema);
        $this->assertArrayHasKey('FirstName', $schema);
        $this->assertArrayHasKey('LastName', $schema);
        $this->assertArrayHasKey('City', $schema);
    }

    /** @test */
    public function it_retrieves_its_summary_columns()
    {
        $summaryCols = SqlDataProviderFactory::defaultSqlDataProvider()->getSummaryColumns();

        $this->assertContains('FirstName', $summaryCols[0]);
        $this->assertContains('First Name', $summaryCols[0]);
        $this->assertContains('LastName', $summaryCols[1]);
        $this->assertContains('Last Name', $summaryCols[1]);
    }

    /** @test */
    public function it_retrieves_a_row_count()
    {
        $provider = SqlDataProviderFactory::defaultSqlDataProvider();
        $countWithoutConditions = $provider->getRowCount();

        $this->assertEquals(53, $countWithoutConditions);
    }

    /**
     * @test
     * @dataProvider jsonFilterData
     */
    public function it_retrieves_an_accurate_row_count_with_json_filters($options, $expectedCount)
    {
        $actualCount = SqlDataProviderFactory::defaultSqlDataProvider()->getRowCount($options);
        $this->assertEquals($expectedCount, $actualCount);
    }

    /** @test */
    public function it_retrieves_multiple_database_rows_with_skip_and_take()
    {
        $rows = SqlDataProviderFactory::defaultSqlDataProvider()->getData([
            'skip'  => 3,
            'take' => 5
        ]);

        $this->assertCount(5, $rows);
        $this->assertEquals('Eduardo', $rows[0]['FirstName']);
        $this->assertEquals('Mark', $rows[4]['FirstName']);
    }

    /**
     * @test
     * @dataProvider jsonFilterData
     */
    public function it_retrieves_multiple_database_rows_with_a_json_filter($options, $expectedCount, $expectedFirstName)
    {
        $rows = SqlDataProviderFactory::defaultSqlDataProvider()->getData($options);

        $this->assertCount($expectedCount, $rows);
        $this->assertEquals($expectedFirstName, $rows[0]['FirstName']);

    }

    /** @test */
    public function it_retrieves_the_order_of_columns_for_its_edit_form()
    {
        $expectedOrder = [
            'CustomerId',
            'FirstName',
            'LastName',
            'City',
            'Email',
        ];
        $actualOrder = SqlDataProviderFactory::defaultSqlDataProvider()->getEditFormOrder();

        $this->assertEquals($expectedOrder, $actualOrder);
    }

    /** @test */
    public function it_creates_an_edit_form_object()
    {
        $form = SqlDataProviderFactory::defaultSqlDataProvider()->getEditForm();
        $this->assertInstanceOf('CrudKit\Util\FormHelper', $form);
    }

    /** @test */
    public function it_creates_a_new_database_row_by_id()
    {
        $expected = [
            'FirstName'     => 'Foo',
            'LastName'      => 'Bar',
            'City'          => 'Baz',
            'Email'         => 'foo@bar.baz'
        ];

        $provider = SqlDataProviderFactory::defaultSqlDataProvider();
        $insertedId = $provider->createItem($expected);

        $inserted = $provider->getRow($insertedId);
        unset($inserted['CustomerId']);

        $this->assertEquals($expected, $inserted);
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function it_fails_when_creating_a_new_row_with_an_invalid_column()
    {
        $values = [
            'Foo'           => 'Bar',
            'FirstName'     => 'Foo',
            'LastName'      => 'Bar',
            'City'          => 'Baz',
            'Email'         => 'foo@bar.baz'
        ];

        SqlDataProviderFactory::defaultSqlDataProvider()->createItem($values);
    }

    /** @test */
    public function it_retrieves_a_database_row_by_id()
    {
        $expected = [
            'CustomerId'    => 1,
            'FirstName'     => 'Luís',
            'LastName'      => 'Gonçalves',
            'City'          => 'São José dos Campos',
            'Email'         => 'luisg@embraer.com.br',
        ];
        $actual = SqlDataProviderFactory::defaultSqlDataProvider()->getRow(1);

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function it_sets_a_database_row_by_id()
    {
        $expectedFirstName = 'Jeb ' . time();
        $provider = SqlDataProviderFactory::defaultSqlDataProvider();
        $provider->setRow(1, ['FirstName' => $expectedFirstName]);
        $updatedRow = $provider->getRow(1);

        $this->assertEquals($expectedFirstName, $updatedRow['FirstName']);
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function it_fails_when_setting_a_database_row_with_an_invalid_column()
    {
        $provider = SqlDataProviderFactory::defaultSqlDataProvider();

        $row = $provider->getRow(1);
        $row['Foo'] = 'Bar';

        $provider->setRow(1, $row);
    }

    /** @test */
    public function it_deletes_a_database_row_by_id()
    {
        $provider = SqlDataProviderFactory::defaultSqlDataProvider();
        $toBeDeleted = $provider->getData(['take' => 1]);

        $rowsAffected = $provider->deleteItem( $toBeDeleted[0]['CustomerId'] );
        $nextItems = $provider->getData(['take' => 1]);

        $this->assertEquals(1, $rowsAffected);
        $this->assertNotEquals($toBeDeleted[0], $nextItems[0]);
    }

    /** @test */
    public function it_deletes_multiple_database_rows_by_id()
    {
        $provider = SqlDataProviderFactory::defaultSqlDataProvider();
        $toBeDeleted = $provider->getData(['take' => 3]);

        $idsToDelete = array_map(function(array $item) {
            return $item['CustomerId'];
        }, $toBeDeleted);

        $rowsAffected = $provider->deleteMultipleItems($idsToDelete);

        $nextItems = $provider->getData(['take' => 3]);
        $nextIds = array_map(function(array $item) {
            return $item['CustomerId'];
        }, $nextItems);

        $this->assertEquals(3, $rowsAffected);
        $this->assertNotEquals($idsToDelete, $nextIds);
    }

    /** @test */
    public function it_validates_a_row_of_data_for_missing_fields()
    {
        $validData = [
            'FirstName' => 'Luís',
            'LastName'  => 'Gonçalves',
            'City'      => 'São José dos Campos',
            'Email'     => 'luisg@embraer.com.br',
        ];
        $invalidData = [
            'FirstName' => 'Luís',
            'City'      => 'São José dos Campos',
            'Email'     => 'luisg@embraer.com.br',
        ];

        $provider = SqlDataProviderFactory::defaultSqlDataProvider();

        $errorsForValidData = $provider->validateRequiredRow($validData);
        $errorsForInvalidData = $provider->validateRequiredRow($invalidData);

        $this->assertEmpty($errorsForValidData);
        $this->assertNotEmpty($errorsForInvalidData);
        $this->assertArrayHasKey('LastName', $errorsForInvalidData);
    }


    /**
     * @test
     * @expectedException \Exception
     */
    public function it_throws_an_exception_if_validating_a_row_for_missing_fields_with_unknown_columns()
    {
        $unknownColumn = ['Foo' => 'Bar'];
        SqlDataProviderFactory::defaultSqlDataProvider()->validateRequiredRow($unknownColumn);
    }

    /** @test */
    public function it_validates_a_row_of_data_using_a_custom_validator()
    {
        $validData = [
            'FirstName' => 'Luís',
            'LastName'  => 'Gonçalves',
            'City'      => 'São José dos Campos',
            'Email'     => 'luisg@embraer.com.br',
        ];

        $invalidData = [
            'FirstName' => 'Luís',
            'LastName'  => 'Gonçalves',
            'City'      => 'São José dos Campos',
            'Email'     => 'I feel this particular string does not conform to the required email format',
        ];

        $provider = SqlDataProviderFactory::defaultSqlDataProviderWithEmailValidator();

        $failuresForValidData = $provider->validateRow($validData);
        $failuresForInvalidData = $provider->validateRow($invalidData);

        $this->assertEmpty($failuresForValidData);
        $this->assertArrayHasKey('Email', $failuresForInvalidData);
        $this->assertEquals($invalidData['Email'], $failuresForInvalidData['Email']);
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function it_throws_an_exception_if_validating_a_row_with_unknown_columns()
    {
        $unknownColumn = ['Foo' => 'Bar'];

        SqlDataProviderFactory::defaultSqlDataProvider()->validateRow($unknownColumn);
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->connection->rollBack();
    }
}
