<?php

namespace App\Controllers;

use App\Models\Tag;
use App\Repositories\MysqlTagsRepository;
use App\Validation\FormValidationException;
use App\Validation\TagsValidator;
use App\ViewRender;
use Godruoyi\Snowflake\Snowflake;

class TagsController
{
    private MysqlTagsRepository $tagsRepository;
    private TagsValidator $tagsValidator;

    public function __construct()
    {
        $this->tagsRepository = new MysqlTagsRepository();
        $this->tagsValidator = new TagsValidator();
    }

    public function tagsForm()
    {
        if (isset($_SESSION['username'])) {
            return new ViewRender('Catalog/tag.twig');
        }
        header('Location: /login');
    }

    public function save()
    {
        try {
            $this->tagsValidator->validate($_POST);
            $id = new Snowflake();
            $tag = new Tag(
                $id->id(),
                $_POST['title']
            );
            $this->tagsRepository->add($tag);

            return new ViewRender('Catalog/tag.twig');

        } catch (FormValidationException $exception) {

            $_SESSION['errors'] = $this->tagsValidator->getErrors();
            return new ViewRender('Catalog/tag.twig', ['errors' => $_SESSION['errors']]);
        }

    }
}