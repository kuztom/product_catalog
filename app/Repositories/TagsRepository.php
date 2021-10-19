<?php

namespace App\Repositories;

use App\Models\Collections\TagsCollection;
use App\Models\Tag;

interface TagsRepository
{
    public function add(Tag $tag): void;

    public function getAll(): TagsCollection;
}