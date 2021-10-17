<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Collections\ProductsCollection;
use App\Models\Product;
use PDO;

require_once 'config.php';

class MysqlProductsRepository implements ProductsRepository
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

    public function add(Product $product): void
    {
        $sql = "INSERT INTO products (id, title, category, qty, created_at, created_by, edited_at) 
                VALUES (?,?,?,?,?,?,?)";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            $product->getId(),
            $product->getTitle(),
            $product->getCategory(),
            $product->getQty(),
            $product->getCreatedAt(),
            $product->getCreatedBy(),
            $product->getEditedAt(),
        ]);
    }

    public function getAll(): ProductsCollection
    {
        $sql = "SELECT * FROM products";
        $stmt = $this->connection->query($sql);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $collection = new ProductsCollection();

        foreach ($products as $product) {
            $collection->add(new Product(
                $product['id'],
                $product['title'],
                $product['category'],
                $product['qty'],
                $product['created_at'],
                $product['created_by'],
                $product['edited_at'],
            ));
        }

        return $collection;
    }

    public function getOne(string $id): ?Product
    {
        $sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$id]);

        $product = $stmt->fetch();

        return new Product(
            $product['id'],
            $product['title'],
            $product['category'],
            $product['qty'],
            $product['created_at'],
            $product['created_by'],
            $product['edited_at'],
        );
    }
}