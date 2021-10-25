<?php

namespace App\Services\Tags;

use App\Models\Tag;
use App\Repositories\MysqlTagsRepository;
use Godruoyi\Snowflake\Snowflake;

class SaveTagsService
{
    private MysqlTagsRepository $tagsRepository;

    public function __construct()
    {
        $this->tagsRepository = new MysqlTagsRepository();
    }

    public function execute(SaveTagsRequest $request): void
    {
        $id = new Snowflake();
        $tag = new Tag(
            $id->id(),
            $request->getTagTitle()
        );
        $this->tagsRepository->add($tag);
    }
}