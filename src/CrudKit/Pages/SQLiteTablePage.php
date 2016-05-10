<?php

namespace CrudKit\Pages;


use Doctrine\DBAL\DriverManager;

class SQLiteTablePage extends BaseSQLDataPage {
	public function __construct ($id, $path) {
        $params = array(
            'driver' => 'pdo_sqlite',
            'path' => $path
        );
        $conn = DriverManager::getConnection($params);
        $this->preInit($id, $conn);

        return $this;
	}
}