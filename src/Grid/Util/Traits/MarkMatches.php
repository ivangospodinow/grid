<?php
namespace Grid\Util\Traits;

use Grid\Column\AbstractColumn;
use Grid\Row\BodyRow;

/**
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
trait MarkMatches
{
    protected $markMatches          = false;
    protected $markMatchesBefore    = '<u>';
    protected $markMatchesAfter     = '</u>';

    public function markMatches(AbstractColumn $column, string $value, array &$data)
    {
        $name  = $column->getName();
        foreach ($data as $row) {
            if (!$row instanceof BodyRow) {
                continue;
            }

            $row[$name] = preg_replace(
                "/({$value})/i",
                $this->markMatchesBefore . "$1" . $this->markMatchesAfter,
                $row[$name]
            );
        }
    }
}