<?php

namespace App\Controllers;

use App\Auth;
use App\Models\Tag;
use App\Repositories\MysqlTagsRepository;
use App\Validation\FormValidationException;
use App\Validation\TitleFormsValidator;
use App\ViewRender;
use Godruoyi\Snowflake\Snowflake;

class TagsController
{
    private MysqlTagsRepository $tagsRepository;
    private TitleFormsValidator $tagsValidator;

    public function __construct()
    {
        $this->tagsRepository = new MysqlTagsRepository();
        $this->tagsValidator = new TitleFormsValidator();
    }

    public function tagsForm(): ViewRender
    {
        if (Auth::loggedIn()) {
            return new ViewRender('Catalog/tag.twig');
        }
        return ViewRender::login();
    }

    public function save(): ViewRender
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