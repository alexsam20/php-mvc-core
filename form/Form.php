<?php

namespace app\core\form;

use app\core\Model;

/**
 * Class Form
 * @package app\core\form
 */
class Form
{
    public static function begin($action, $method): self
    {
        echo sprintf('<form action="%s" method="%s">', $action, $method);
        return new Form();
    }

    public static function end(): string
    {
        return  '</form>';
    }

    public function field(Model $model, $attribute): Input
    {
        return new Input($model, $attribute);
    }
}