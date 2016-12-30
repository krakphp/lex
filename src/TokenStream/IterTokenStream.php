<?php

namespace Krak\Lex\TokenStream;

use Krak\Lex,
    Iterator;

class IterTokenStream implements Lex\TokenStream
{
    private $stream;
    private $tok_cache;

    public function __construct(Iterator $stream) {
        $this->stream = $stream;
    }

    public function getToken() {
        if ($this->tok_cache) {
            $ret = $this->tok_cache;
            $this->tok_cache = null;
            return $ret;
        }

        $tok = $this->stream->current();

        if (!$tok) {
            return;
        }

        $this->stream->next();

        return $tok;
    }

    public function peek() {
        if ($this->tok_cache) {
            return $this->tok_cache;
        }

        $this->tok_cache = $this->getToken();
        return $this->tok_cache;
    }

    public function isEmpty() {
        return $this->peek() === null;
    }

    public function getIterator() {
        return $this->stream;
    }
}
