<?php

namespace App;

class ViewRender
{
    private string $template;
    private array $vars;

    public function __construct(string $template, array $vars = [])
    {
        $this->template = $template;
        $this->vars = $vars;
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

    public static function newProduct($categories, $tags)
    {
        return new ViewRender('Catalog/add.twig', [
            'categories' => $categories,
            'tags' => $tags
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


}