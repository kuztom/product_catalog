<?php

namespace App\Controllers;

use App\Models\Tag;
use App\Repositories\MysqlCategoriesRepository;
use App\Repositories\MysqlTagsRepository;
use App\ViewRender;
use Godruoyi\Snowflake\Snowflake;

class TagsController
{
    private MysqlTagsRepository $tagsRepository;
    private MysqlCategoriesRepository $categoriesRepository;

    public function __construct()
    {
        $this->tagsRepository = new MysqlTagsRepository();
        $this->categoriesRepository = new MysqlCategoriesRepository();
    }

    public function tagsForm(): ViewRender
    {
        return new ViewRender('Catalog/tag.twig');
    }

    public function save(): ViewRender
    {
        $id = new Snowflake();
        $tag = new Tag(
            $id->id(),
            $_POST['title']
        );
        $this->tagsRepository->add($tag);

        return new ViewRender('Catalog/tag.twig');
    }
}