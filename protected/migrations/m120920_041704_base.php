<?php

class m120920_041704_base extends CDbMigration
{
    public function up()
    {
        $dumpFile = dirname(__FILE__) . '/base.sql';

        $db = $this->dbConnection;
        $dbUser = $db->username;
        $dbPass = $db->password;
        if (preg_match('/host=([^;]*)/', $db->connectionString, $regs)) {
            $dbHost = $regs[1];
        } else {
            throw new CException('can\'t get mysql host from connectionString');
        }
        if (preg_match('/dbname=([^;]*)/', $db->connectionString, $regs)) {
            $dbName = $regs[1];
        } else {
            throw new CException('can\'t get mysql db name from connectionString');
        }

        system('mysql --host=' . $dbHost . ' --user=' . $dbUser . ' --password=' . $dbPass . ' ' . $dbName . ' < ' . realpath($dumpFile));
    }

    public function down()
    {
        echo "m120920_041704_base does not support migration down.\n";
        return false;
    }
}