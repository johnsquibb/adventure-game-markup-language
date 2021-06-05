# Adventure Game Markup Language

A set of utilities for working with Adventure Game Markup Language (AGML) to build games for
the [Adventure Game Framework](https://github.com/johnsquibb/adventure-game-framework).

## Key Features

- Defines and documents AGML syntax.
- Provides sample code for transpiling AGML into hydrator objects that can be used with the
  framework.

## AGML Syntax

The following snippet is valid AGML and demonstrates the use of: types, comments, literals,
identifiers, assignments, list assignments, and multiline text assignments.

```AGML
# Type Declaration.
# Types must be UPPERCASE.
[ITEM]

# Any line beginning with '#' will be ignored.
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
Description #1
Description #2              
Description #3              
# ...

# Another multi-line assignment
[text]
Text #1
Text #2
Text #3
# ...

# To use reserved symbols in assignments, escape them with Backslash.
# Examples:
#             \,
#             \\
#             \=
#             \[
#             \]
```

## Using the Transpiler

The following PHP code snippet demonstrates transpiler usage.

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