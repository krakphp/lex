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
