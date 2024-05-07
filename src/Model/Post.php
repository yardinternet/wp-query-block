<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Model;

class Post extends \Corcel\Model\Post
{
    /**
     * Override the getAcfAttribute method, as the corcel/acf
     * package is currently not being maintained.
     */
    public function getAcfAttribute()
    {
        return null;
    }
}
