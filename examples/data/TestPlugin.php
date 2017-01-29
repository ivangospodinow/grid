<?php

use Grid\Interfaces\RenderPluginInterface;

use Grid\Util\Traits\GridAwareTrait;
use Grid\Interfaces\GridInterface;
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

        foreach ($this->getGrid()[InputsInterface::class] as $plugin) {
            foreach ($plugin->getInputs() as $input) {
                if ($input->getAttribute('type') === $input::TYPE_BUTTON) {
                    $input->addAttribute('class', ' btn btn-default');
                } else {
                    $input->addAttribute('class', ' form-control');
                }
            }
        }

        $html .= '
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<link rel="stylesheet" href="//cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<style type="text/css">
.grid-action-button
{
margin-right:10px;
}
</style>

<script
			  src="https://code.jquery.com/jquery-3.1.1.slim.min.js"
			  integrity="sha256-/SIrNqv8h6QGKDuNoLGA4iret+kyesCkHGzVUUV0shc="
			  crossorigin="anonymous"></script>

<script src="//cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
';



        return $html;
    }

    /**
     *
     * @param string $html
     */
    public function postRender(string $html) : string
    {
        return $html;
    }
}