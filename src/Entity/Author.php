<?php

namespace App\Entity;

class Author
{
    public function __construct(
        public int $id,
        public string $name
    )
    {
    }
}
