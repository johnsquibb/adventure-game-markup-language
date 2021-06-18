<?php

namespace AdventureGameMarkupLanguage\Test;

use AdventureGameMarkupLanguage\Lexer;
use AdventureGameMarkupLanguage\Parser;
use AdventureGameMarkupLanguage\Transpiler;
use PHPUnit\Framework\TestCase;

class ReadmeExamplesTest extends TestCase
{
    public function testSpecificationExample()
    {
        $markup = <<<END
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
        END;

        // Transpile AGML into Hydrator objects.
        $lexer = new Lexer();
        $parser = new Parser();
        $transpiler = new Transpiler($lexer, $parser);

        $hydrators = $transpiler->transpile($markup);

        $this->assertCount(1, $hydrators);
    }
}