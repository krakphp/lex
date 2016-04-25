<?php

namespace Krak\Lex;

use Krak\Coll\Set;
use function iter\filter;

function _match_token($token_map, $input, $offset) {
    $matches = [];
    foreach ($token_map as $re => $token) {
        if (preg_match($re, $input, $matches, PREG_OFFSET_CAPTURE, $offset)) {
            return new MatchedToken($matches[0][0], $token, $matches[0][1]);
        }
    }

    return null;
}

function lexer($token_map, $throw = true) {
    return function($input) use ($token_map, $throw) {
        $maxlen = strlen($input);
        $offset = 0;

        while ($offset < $maxlen) {
            $mtok = _match_token($token_map, $input, $offset);

            // unrecognized input
            if (!$mtok) {
                if (!$throw) {
                    return;
                }

                throw new LexException('Unrecognized Input');
            }

            $offset = $mtok->getNextOffset();
            yield $mtok;
        }
    };
}

function skipLexer($lex, $tokens) {
    if ($tokens instanceof Set\ConstSet == false) {
        $tokens = Set\ArraySet::create($tokens);
    }

    return function($input) use ($lex, $tokens) {
        return filter(function($mtok) use ($tokens) {
            return !$tokens->has($mtok->token);
        }, $lex($input));
    };
}

function mockLexer($tokens) {
    return function($input) use ($tokens) {
        return $tokens;
    };
}
