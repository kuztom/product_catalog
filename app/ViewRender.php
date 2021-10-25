<?php

namespace App;

class ViewRender
{
    private string $template;
    private array $vars;
    private array $errors;

    public function __construct(string $template, array $vars = [], array $errors = [])
    {
        $this->template = $template;
        $this->vars = $vars;
        $this->errors = $errors;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getVars(): array
    {
        return $this->vars;
    }

    public static function catalog($products, $categories, $tags)
    {
        return new ViewRender('Catalog/catalog.twig', [
            'products' => $products,
            'categories' => $categories,
            'tags' => $tags
        ]);
    }

    public static function newProduct($categories, $tags, $errors)
    {
        return new ViewRender('Catalog/add.twig', [
            'categories' => $categories,
            'tags' => $tags,
            'errors' => $errors

        ]);
    }

    public static function product($product, $categories)
    {
        return new ViewRender('Catalog/product.twig', [
            'product' => $product,
            'categories' => $categories
        ]);
    }

    public static function login()
    {
        return new ViewRender('Users/login.twig');
    }

    public static function register()
    {
        return new ViewRender('Users/register.twig');
    }


}