<?php

namespace App;

class Spam
{
    public function detect($body)
    {
        $this->detectInvalidKeyWords($body);

        return false;
    }

    protected function detectInvalidKeyWords($body)
    {
        $invalidKeywords = [
            'yahoo customer support'
        ];

        foreach ($invalidKeywords as $keyword) {
            if (stripos($body, $keyword) !== false) {
                throw new \Exception('Your reply contains spam.');
            }
        }
    }
}
