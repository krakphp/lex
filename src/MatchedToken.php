<?php

namespace Krak\Lex;

class MatchedToken {
    public $match;
    public $token;
    public $offset;

    public function __construct($match, $token, $offset) {
        $this->match = $match;
        $this->token = $token;
        $this->offset = $offset;
    }

    public function getNextOffset() {
        return $this->offset + strlen($this->match);
    }
}
