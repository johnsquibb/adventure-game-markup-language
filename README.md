# adventure-game-markup-language

A parser for [adventure-game-framework](https://github.com/johnsquibb/adventure-game-framework)
files.

## Specification

``` 
[TYPE]              e.g. ITEM, CONTAINER, PORTAL, LOCATION, etc. must be in all CAPS

attribtute=value    for single value or single line entries. Comma-Separated-Value (CSV) lists can 
                    be used for attributes that accept multiple parameters.

[section name]      for multi-line or nested parameter input attribute=value directives under a 
                    [section name] apply to that section only.

# Comment           anything following the '#' symbol is ignored to end of line.


Any number of [TYPE] sections may be defined in order to support related items together.
e.g. portals in locations, or trigger/events with associated items.

Types

Strings                 Strings are entered as raw text following a [section name], or as attribute 
                        assignment following '=' to the end of line.

Boolean                 Accepts: true/false, yes/no, 1/0

Number                  Numbers are entered in the same way as strings.

Special Characters      To use AGML reserved characters in text escape them with Backslash.
                        e.g.
                                \# 
                                \,
                                \[
                                \]
                                \=
```
