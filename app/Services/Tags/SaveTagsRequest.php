<?php

namespace App\Services\Tags;

class SaveTagsRequest
{
    private string $tagTitle;

    public function __construct(string $tagTitle)
    {
        $this->tagTitle = $tagTitle;
    }

    public function getTagTitle(): string
    {
        return $this->tagTitle;
    }
}