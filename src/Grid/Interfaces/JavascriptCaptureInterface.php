<?php

namespace Grid\Interfaces;

/**
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
interface JavascriptCaptureInterface
{
    public function __toString() : string;
    public function captureStart();
    public function captureEnd();
}
