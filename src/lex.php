<?php

namespace Krak\Lex;

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

function skipLexer(callable $lex, array $tokens) {
    return function($input) use ($lex, $tokens) {
        foreach ($lex($input) as $mtok) {
            if (!in_array($mtok->token, $tokens)) {
                yield $mtok;
            }
        }
    };
}

/** converts the lex output into a token stream */
function tokenStreamLexer($lex) {
    return function($input) use ($lex) {
        return new TokenStream\IterTokenStream($lex($input));
    };
}

function mockLexer($tokens) {
    return function($input) use ($tokens) {
        return $tokens;
    };
}
