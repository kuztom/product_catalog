<?php

namespace App\Repositories;

use App\Models\Collections\TagsCollection;
use App\Models\Tag;
use PDO;

require_once 'config.php';

class MysqlTagsRepository implements TagsRepository
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

    public function add(Tag $tag): void
    {
        $sql = "INSERT INTO tags (id, title) 
                VALUES (?,?)";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            $tag->getId(),
            $tag->getTitle(),
        ]);

    }

    public function getAll(): TagsCollection
    {
        $sql = "SELECT * FROM tags";
        $stmt = $this->connection->query($sql);
        $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $collection = new TagsCollection();

        foreach ($tags as $tag) {
            $collection->add(new Tag(
                $tag['id'],
                $tag['title']
            ));
        }

        return $collection;
    }

    public function getAvailableTags(): TagsCollection
    {
        $sql = "SELECT tag_id, title FROM tags
                RIGHT JOIN products_tags
                ON tags.id = products_tags.tag_id";
        $stmt = $this->connection->query($sql);
        $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $collection = new TagsCollection();
        foreach ($tags as $tag) {
            $collection->add(new Tag(
                $tag['tag_id'],
                $tag['title']
            ));
        }
        return $collection;
    }

}