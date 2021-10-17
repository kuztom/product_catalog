<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Collections\CategoriesCollection;
use PDO;

require_once 'config.php';

class MysqlCategoriesRepository implements CategoriesRepository
{
    private PDO $connection;

    public function __construct()
    {
        $host = DB_HOST;
        $db = DB_DATABASE;
        $db_user = DB_USERNAME;
        $pass = DB_PASSWORD;

        $dsn = "mysql:host=$host;dbname=$db;charset=UTF8";
        try {
            $this->connection = new \PDO($dsn, $db_user, $pass);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function add(Category $category): void
    {
        $sql = "INSERT INTO categories (id, title) 
                VALUES (?,?)";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            $category->getId(),
            $category->getTitle(),
        ]);
    }

    public function getAll(): CategoriesCollection
    {
        $sql = "SELECT * FROM categories";
        $stmt = $this->connection->query($sql);
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $collection = new CategoriesCollection();

        foreach ($categories as $category){
            $collection->add(new Category(
               $category['id'],
                $category['title']
            ));
        }

        return $collection;
    }
}