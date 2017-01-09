<?php
namespace Grid\Renderer;

use Grid\Interfaces\SourcePluginInterface;
use Grid\Interfaces\JavascriptPluginInterface;
use Grid\Interfaces\RenderPluginInterface;
use Grid\Interfaces\JavascriptCaptureInterface;
use Grid\Util\Traits\GridAwareTrait;
use Grid\Interfaces\GridInterface;
use Grid\Source\AbstractSource;

use Grid\Plugin\PaginationPlugin;
use Grid\Plugin\ColumnSortablePlugin;
use Grid\Plugin\ColumnFilterablePlugin;

/**
 *
 * @author Gospodinow
 */
class DataTablesRenderer extends HtmlRenderer implements
    JavascriptPluginInterface,
    SourcePluginInterface,
    RenderPluginInterface,
    GridInterface
{
    use GridAwareTrait;

    protected $dataTableParams = [];
    
    public function preRender(string $html) : string
    {
        $grid = $this->getGrid();
        $params = $this->dataTableParams;
        if (!array_key_exists('paging', $params)) {
            $params['paging'] = false;
            foreach ($grid[PaginationPlugin::class] as $pagination) {
                $params['paging'] = true;
            }
        }
        unset($grid[PaginationPlugin::class]);

        if (!array_key_exists('ordering', $params)) {
            $params['ordering'] = false;
            foreach ($grid[ColumnSortablePlugin::class] as $pagination) {
                $params['ordering'] = true;
            }
        }
        unset($grid[ColumnSortablePlugin::class]);
        unset($grid[ColumnFilterablePlugin::class]);
        
        $this->dataTableParams = $params;

        return $html;
    }

    public function postRender(string $html) : string
    {
        return $html;
    }

    /**
     *
     * @param AbstractSource $source
     */
    public function filterSource(AbstractSource $source) : AbstractSource
    {
        $source->setStart(0);
        $source->setEnd(0);
        return $source;
    }

    /**
     *
     * @param JavascriptCaptureInterface $script
     */
    public function addJavascript(JavascriptCaptureInterface $script) : JavascriptCaptureInterface
    {
        $script->captureStart();
        ?>
        //<script>
            (function ($, id, params) {
                $(document).ready(function(){
                    $('#' + id).DataTable(params);
                });
            })(jQuery, '<?php echo $this->getGrid()->getId()?>', <?php echo json_encode($this->dataTableParams)?>);
        //</script>
        <?php
        $script->captureEnd();        
        return $script;
    }
}
