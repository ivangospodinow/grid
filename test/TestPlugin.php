<?php

use Grid\Plugin\Interfaces\RenderPluginInterface;

use Grid\Util\Traits\GridAwareTrait;
use Grid\GridInterface;
use Grid\Interfaces\InputsInterface;

class TestPlugin implements RenderPluginInterface, GridInterface
{
    use GridAwareTrait;
    
    /**
     *
     */
    public function preRender(string $html) : string
    {
        $this->getGrid()->addAttribute('class', 'table table-hover');

        foreach ($this->getGrid()->getObjects(InputsInterface::class) as $plugin) {
            foreach ($plugin->getInputs() as $input) {
                $input->addAttribute('class', 'form-control');
            }
        }

        $html .= '
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">';

        return $html;
    }

    /**
     *
     * @param string $html
     */
    public function postRender(string $html) : string
    {
        $html .= '';

        return $html;
    }
}