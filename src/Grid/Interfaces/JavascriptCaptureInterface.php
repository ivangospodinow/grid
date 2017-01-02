<?php

namespace Grid\Interfaces;

interface JavascriptCaptureInterface
{
    public function __toString() : string;
    public function captureStart();
    public function captureEnd();
}
