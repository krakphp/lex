<?php

use Krak\Lex\MatchedToken;

use function Krak\Lex\lexer,
    Krak\Lex\skipLexer,
    Krak\Lex\mockLexer;

describe('Lex', function() {
    describe('#mockLexer', function() {
        it('creates a lexer that returns whatever was passed', function() {
            $lex = mockLexer('a');
            assert($lex('') === 'a');
        });
    });
    describe('#skipLexer', function() {
        it('creates a lexer decorator that will skip certain matched tokens', function() {
            $lex = mockLexer([
                new MatchedToken('', 'a', 0),
                new MatchedToken('', 'b', 0),
                new MatchedToken('', 'c', 0),
            ]);
            $lex = skipLexer($lex, ['b']);

            $toks = iter\map(function($mtok) { return $mtok->token; }, $lex(''));
            $toks = iter\toArray($toks);
            assert($toks == ['a', 'c']);
        });
    });
    describe('#lexer', function() {
        it('uses regexps to tokenize input', function() {
            $lex = lexer([
                '/a+/A' => 'a',
                '/b+/A' => 'b',
            ]);
            $toks = $lex('abbaaa');
            $toks = iter\toArray($toks);

            assert(
                $toks[0]->match == 'a' &&
                $toks[0]->token == 'a' &&
                $toks[0]->offset == 0 &&
                $toks[1]->match == 'bb' &&
                $toks[1]->token == 'b' &&
                $toks[1]->offset == 1 &&
                $toks[2]->match == 'aaa' &&
                $toks[2]->token == 'a' &&
                $toks[2]->offset == 3
            );
        });
    });
});
