<?php

namespace App\Controllers;

use App\Auth;
use App\Services\Tags\SaveTagsRequest;
use App\Services\Tags\SaveTagsService;
use App\Validation\FormValidationException;
use App\Validation\TitleFormsValidator;
use App\ViewRender;

class TagsController
{
    private TitleFormsValidator $tagsValidator;
    private SaveTagsService $saveTagsService;

    public function __construct()
    {
        $this->tagsValidator = new TitleFormsValidator();
        $this->saveTagsService = new SaveTagsService();
    }

    public function tagsForm(): ViewRender
    {
        if (Auth::loggedIn()) {
            return ViewRender::newTag();
        }
        return ViewRender::login();
    }

    public function save(): ViewRender
    {
        $tagTitle = $_POST['title'];

        try {
            $this->tagsValidator->validate($_POST);
            $this->saveTagsService->execute(new SaveTagsRequest($tagTitle));

            return ViewRender::newTag();

        } catch (FormValidationException $exception) {

            $_SESSION['errors'] = $this->tagsValidator->getErrors();
            return new ViewRender('Catalog/tag.twig', ['errors' => $_SESSION['errors']]);
        }

    }
}