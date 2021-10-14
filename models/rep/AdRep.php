<?php

namespace models\rep;

use app\IRepository;
require './../app/IRepository.php';
use PDO;

class AdRep implements IRepository
{
    public function save($model)
    {
    }

    public function get($id)
    {
        $dbh = new PDO('mysql:host=localhost;dbname=ads', 'ad-api', 'password');

        $sth = $dbh->prepare('SELECT * FROM ad WHERE id = :id');
        $sth->execute([':id' => $id]);

        $row = $sth->fetch(PDO::FETCH_ASSOC);

        $dbh = null;
        $sth = null;

        return $row;
    }

    public function getRelevant()
    {
    }
}