<?php

namespace CrudKit\Laravel;

use CrudKit\Pages\BaseSQLDataPage;
use Doctrine\DBAL\DriverManager;
use DB;

class LaravelTablePage extends BaseSQLDataPage {
    public function __construct ($id) {
    	$pdo = DB::connection()->getPdo();
        $params = array(
            'pdo' => $pdo
        );

        $conn = DriverManager::getConnection($params);
        $this->preInit($id, $conn);

        return $this;
    }
}