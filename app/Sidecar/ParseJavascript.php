<?php
namespace App\Sidecar;

use Hammerstone\Sidecar\LambdaFunction;

class ParseJavascript extends LambdaFunction
{
    public function handler()
    {
        return "parse.handle";
    }

    public function package()
    {
        return [
          'parse.js'
        ];
    }
}
