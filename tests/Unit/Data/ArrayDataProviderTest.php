<?php
namespace CrudKitTests\Unit\Data;

use CrudKitTests\ArrayDataProviderFactory;
use CrudKitTests\Unit\CrudKitTest;

class ArrayDataProviderTest extends CrudKitTest
{
    /** @test */
    public function it_retrieves_a_schema_based_on_defined_columns()
    {
        $provider = ArrayDataProviderFactory::defaultArrayDataProvider();
        $schema = $provider->getSchema();

        $this->assertArrayHasKey('FirstName', $schema);
        $this->assertArrayHasKey('LastName', $schema);
        $this->assertArrayHasKey('City', $schema);
        $this->assertArrayHasKey('Email', $schema);
    }

    /** @test */
    public function it_retrieves_its_summary_columns()
    {
        $summaryCols = ArrayDataProviderFactory::defaultArrayDataProvider()->getSummaryColumns();

        $this->assertContains('FirstName', $summaryCols[0]);
        $this->assertContains('First Name', $summaryCols[0]);
        $this->assertContains('LastName', $summaryCols[1]);
        $this->assertContains('Last Name', $summaryCols[1]);
    }

    /** @test */
    public function it_retrieves_a_row_count()
    {
        $provider = ArrayDataProviderFactory::defaultArrayDataProvider(7);
        $countWithoutConditions = $provider->getRowCount();

        $this->assertEquals(7, $countWithoutConditions);
    }

    /** @test */
    public function it_retrieves_multiple_rows_with_skip_and_take()
    {
        $rows = ArrayDataProviderFactory::defaultArrayDataProvider()->getData([
            'skip'  => 3,
            'take'  => 5
        ]);

        $this->assertCount(5, $rows);
        $this->assertEquals('First Name #3', $rows[0]['FirstName']);
        $this->assertEquals('First Name #7', $rows[4]['FirstName']);
    }

    /** @test */
    public function it_retrieves_the_order_of_columns_for_its_edit_form()
    {
        $expectedOrder = [
            'FirstName',
            'LastName',
            'City',
            'Email',
        ];
        $actualOrder = ArrayDataProviderFactory::defaultArrayDataProvider()->getEditFormOrder();

        $this->assertEquals($expectedOrder, $actualOrder);
    }

    /** @test */
    public function it_creates_an_edit_form_object()
    {
        $form = ArrayDataProviderFactory::defaultArrayDataProvider()->getEditForm();
        $this->assertInstanceOf('CrudKit\Util\FormHelper', $form);
    }

    /** @test */
    public function it_adds_a_row_by_id()
    {
        $expected = [
            'FirstName'     => 'Foo',
            'LastName'      => 'Bar',
            'City'          => 'Baz',
            'Email'         => 'foo@bar.baz'
        ];

        $provider = ArrayDataProviderFactory::defaultArrayDataProvider();
        $insertedId = $provider->createItem($expected);

        $inserted = $provider->getRow($insertedId);

        $this->assertEquals($expected, $inserted);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
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

        ArrayDataProviderFactory::defaultArrayDataProvider()->createItem($values);
    }

    /** @test */
    public function it_retrieves_a_row_by_index()
    {
        $actual = ArrayDataProviderFactory::defaultArrayDataProvider()->getRow(1);

        $this->assertArrayHasKey('FirstName', $actual);
        $this->assertEquals('First Name #1', $actual['FirstName']);
    }

    /** @test */
    public function it_sets_a_row_by_index()
    {
        $expectedFirstName = 'Jeb ' . time();
        $provider = ArrayDataProviderFactory::defaultArrayDataProvider();
        $provider->setRow(1, ['FirstName' => $expectedFirstName]);
        $updatedRow = $provider->getRow(1);

        $this->assertEquals($expectedFirstName, $updatedRow['FirstName']);
    }

    /** @test */
    public function it_returns_false_when_setting_an_invalid_row_by_index()
    {
        $wasSet = ArrayDataProviderFactory::defaultArrayDataProviderWithEmailValidator()
            ->setRow(1, ['Email' => 'not a valid email address']);

        $this->assertFalse($wasSet);
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function it_fails_when_setting_a_row_with_an_invalid_column()
    {
        $provider = ArrayDataProviderFactory::defaultArrayDataProvider();

        $row = $provider->getRow(1);
        $row['Foo'] = 'Bar';

        $provider->setRow(1, $row);
    }

    /** @test */
    public function it_deletes_a_row_by_index()
    {
        $provider = ArrayDataProviderFactory::defaultArrayDataProvider();
        $toBeDeleted = $provider->getData(['take' => 1]);

        $rowsAffected = $provider->deleteItem(0);
        $nextItems = $provider->getData(['take' => 1]);

        $this->assertEquals(1, $rowsAffected);
        $this->assertNotEquals($toBeDeleted[0], $nextItems[0]);
    }

    /** @test */
    public function it_returns_false_when_deleting_a_non_existent_index()
    {
        $wasDeleted = ArrayDataProviderFactory::defaultArrayDataProvider()->deleteItem(9999);
        $this->assertFalse($wasDeleted);
    }

    /** @test */
    public function it_deletes_multiple_rows_by_index()
    {
        $provider = ArrayDataProviderFactory::defaultArrayDataProvider();
        $idsToDelete = [0, 1, 2];
        $rowsAffected = $provider->deleteMultipleItems($idsToDelete);
        $rowCount = $provider->getRowCount();

        $this->assertEquals(3, $rowsAffected);
        $this->assertEquals(7, $rowCount);
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

        $provider = ArrayDataProviderFactory::defaultArrayDataProvider();

        $errorsForValidData = $provider->validateRequiredRow($validData);
        $errorsForInvalidData = $provider->validateRequiredRow($invalidData);

        $this->assertEmpty($errorsForValidData);
        $this->assertNotEmpty($errorsForInvalidData);
        $this->assertArrayHasKey('LastName', $errorsForInvalidData);
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

        $provider = ArrayDataProviderFactory::defaultArrayDataProviderWithEmailValidator();

        $failuresForValidData = $provider->validateRow($validData);
        $failuresForInvalidData = $provider->validateRow($invalidData);

        $this->assertEmpty($failuresForValidData);
        $this->assertArrayHasKey('Email', $failuresForInvalidData);
        $this->assertEquals($invalidData['Email'], $failuresForInvalidData['Email']);
    }
}
