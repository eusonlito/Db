<?php
namespace ANS\Db\Drivers;

class Mysql implements \ANS\Db\Idrivers
{
    /**
    * public function getDSN (array $settings)
    *
    * return the DSN string
    *
    * return string
    */
    public function getDSN ($settings)
    {
        $dsn = 'mysql:host='.$settings['host'];

        if ($settings['port']) {
            $dsn .= ';port='.$settings['port'];
        } elseif ($settings['unix_socket']) {
            $dsn .= ';unix_socket='.$settings['unix_socket'];
        }

        $dsn .= ';dbname='.$settings['database'];

        if ($settings['charset']) {
            $dsn .= ';charset='.$settings['charset'];
        }

        return $dsn;
    }
}
