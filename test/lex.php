<?php

use Krak\Lex\TokenStream,
    Krak\Lex\MatchedToken;

use function Krak\Lex\lexer,
    Krak\Lex\skipLexer,
    Krak\Lex\mockLexer,
    Krak\Lex\tokenStreamLexer;

use Krak\Fun\{f, c};

describe('Krak Lex', function() {
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

            $toks = f\arrayMap(function($mtok) { return $mtok->token; }, $lex(''));
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
            $toks = f\toArray($toks);

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
    describe('#tokenStreamLexer', function() {
        it('returns an instance of TokenStream', function() {
            $gen = function() { yield 'a'; yield 'b'; };
            $lex = mockLexer($gen());
            $lex = tokenStreamLexer($lex);
            $toks = $lex('');
            assert($toks instanceof TokenStream);
        });
    });
    describe('TokenStream IterTokenStream', function() {
        it('implements a token stream from an Iterator', function() {
            $gen = function() { yield 'a'; yield 'b'; };
            $stream = new TokenStream\IterTokenStream($gen());

            assert($stream->peek() == $stream->getToken());
            $stream->getToken();
            assert($stream->isEmpty());
        });
    });
});
