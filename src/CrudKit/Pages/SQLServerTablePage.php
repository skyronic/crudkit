<?php

namespace CrudKit\Pages;

use CrudKit\Pages\BaseSQLDataPage;
use Doctrine\DBAL\DriverManager;

class SQLServerTablePage extends BaseSQLDataPage {
    public function __construct ($id, $user, $pass, $db, $extra = array()) {
        $params = array(
            'driver' => 'sqlsrv',
            'user' => $user,
            'password' => $pass,
            'dbname' => $db
        );

        if(isset($extra['host'])) {
            $params['host'] = $extra['host'];
        }
        if(isset($extra['port'])) {
            $params['port'] = $extra['port'];
        }
        if(isset($extra['charset'])) {
            $params['charset'] = $extra['charset'];
        }
        $conn = DriverManager::getConnection($params);
        $this->preInit($id, $conn);

        return $this;
    }
}