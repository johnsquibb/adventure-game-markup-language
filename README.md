# adventure-game-markup-language

Transpiles Adventure Game Markup Language (AGML) into hydrator objects for use in building games
that utilize the [Adventure Game Framework](https://github.com/johnsquibb/adventure-game-framework).

## Example

The following snippet demonstrates the use of the common AGML components: types, comments, literals,
identifiers, assignments, list assignments, and multiline text assignments.

```
# Type Declaration.
# Types must be UPPERCASE.
[ITEM]

# Anything following a '#' charcter is ignored.
# This is a comment

# An assignment.
# Format is variable=value
id=flashlight

# Booleans assignments
# 'yes' yields true.
acquirable=yes
# Any other value yields false.
deactivatable=no

# CSV list assignment produces array of values.
# Format is variable=value,value,..
tags=flashlight,light,magic torch stick

# Multiline assignment. Variable name in brackets must be lowercase.
# Any number of lines may follow, and the parser will continue until reaching a new instruction.
[description]
Line 1
Line 2
Line 3
...

# Another multi-line assignment
[text]
Line 1
Line 2
Line 3
...

# To use special characters, escape them with '\'.

```

The following PHP code snippet demonstrates transpiler usage:

```php 
$markup = <<<END
[ITEM]
# Attributes
id=flashlight
size =2
readable = yes
name = Small Flashlight

# Interactions
acquirable=yes
activatable=yes
deactivatable=no

# Tags 
tags=flashlight,light,magic torch stick

[description]
A black metal flashlight that runs on rechargeable batteries.
There is a round gray button for activating it.
There is some small text printed on a label on the side of the flashlight.

[text]
Information written on the side:
Model: Illuminated Devices Inc
Year: 1983
Serial Number: 8301IDI001256703
Batt. Type: (4) AA
END;

// Transpile AGML into Hydrator objects.
$lexer = new Lexer();
$parser = new Parser();
$transpiler = new Transpiler($lexer, $parser);

$hydrators = $transpiler->transpile($markup);
```

## Specification

See [AGML Specification](docs/AGML%20spec.txt)