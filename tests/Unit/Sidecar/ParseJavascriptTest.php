<?php

namespace Tests\Unit\Sidecar;

use Tests\TestCase;
use App\Sidecar\ParseJavascript;

class ParseJavascriptTest extends TestCase
{
    /** @test **/
    public function it_can_parse_javascript()
    {
        $js = <<<JS
            {
                "product": {
                    "name": "Example Product"
                }
            }
        JS;

        $result = ParseJavascript::execute([
            'code' => $js
        ])->body();

        $this->assertEquals(['product' => ['name' => 'Example Product']], $result);
    }
}
