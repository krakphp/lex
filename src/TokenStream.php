<?php

namespace Krak\Lex;

use IteratorAggregate;

interface TokenStream extends IteratorAggregate {
    public function getToken();
    public function peek();
    public function isEmpty();
}
