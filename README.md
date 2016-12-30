# Lex

Lex is a library for lexical analysis in PHP. Currently, only simple regular expression lexers are available; but considering that you shouldn't be lexing anything complex in php, this should be fine :).

## Installation

```
composer require krak/lex
```

## Usage

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use function Krak\Lex\lexer,
    Krak\Lex\skipLexer;

const TOK_INT = 'int';
const TOK_PLUS = 'plus';
const TOK_MINUS = 'minus';
const TOK_WS = 'whitespace';

// creates a lexer that will use these RE's to match input
// the A (anchor flag) is required
$lex = lexer([
    '/\d+/A' => TOK_INT,
    '/\+/A' => TOK_PLUS,
    '/\-/A' => TOK_MINUS,
    '/\s+/A' => TOK_WS
]);

// decorator for skipping tokens, in this case, just throw away the whitespace tokens
$lex = skipLexer($lex, [TOK_WS]);

// lex the input and return an iterator of tokens
$toks = $lex('1 + 2 - 3');

foreach ($toks as $matched_tok) {
    printf(
        "Matched token '%s' with input '%s' at offset %d\n",
        $matched_tok->token,
        $matched_tok->match,
        $matched_tok->offset
    );
}
```

The following program would output

```
Matched token 'int' with input '1' at offset 0
Matched token 'plus' with input '+' at offset 2
Matched token 'int' with input '2' at offset 4
Matched token 'minus' with input '-' at offset 6
Matched token 'int' with input '3' at offset 8
```

## TokenStream

A token stream is a simple interface for consuming one token at a time. This is very useful for [Recursive Decent Parsers](https://en.wikipedia.org/wiki/Recursive_descent_parser)

```php
<?php

use function Krak\Lex\lexer,
    Krak\Lex\tokenStreamLexer;

$lex = lexer(['/a/A' => 'a', '/b/A' => 'b']);
$lex = tokenStreamLexer($lex);
$stream = $lex('aba');

assert($stream->peek() == 'a');
assert($stream->getToken() == 'a');
assert($stream->getToken() == 'b');
assert($stream->getToken() == 'a');
assert($stream->isEmpty());
```

## API

### Lexers

Each lexer will accept a string input and then return an iterable of `MatchedToken`

#### lexer($token_map, $throw = true)

Main lexer which lexes the strings based off of the `$token_map`. `$throw` determines whether or not the lexer should throw an exception on unrecognized input.

#### skipLexer($lex, $tokens)

Lexer decorator which will skip any Matched Tokens in the set of the `$tokens` passed in.

#### tokenStreamLexer($lex)

Lexer decorator that will convert the output of the `$lex` into a `TokenStream`

#### mockLexer($tokens)

Returns `$tokens` as is.

### class MatchedToken

#### $match

Returns the text that was matched.

#### $token

Returns the token name that was matched.

#### $offset

Returns the string offset at which the match started.

### interface TokenStream extends \\IteratorAggregate

#### getToken()

Returns the current token and advances the internal pointer up by one.

#### peek()

Returns the current token but does *not* advance the internal pointer

#### isEmpty()

returns true if the token stream is empty, false if not.
