<?php
namespace Grid\Util\Traits;

use Grid\Util\LinkCreator;
use Grid\GridInterface;

/**
 *
 * @author Gospodinow
 */
trait LinkCreatorAwareTrait
{
    protected $linkCreator;

    /**
     *
     * @return \Grid\Util\LinkCreator
     */
    public function getLinkCreator() : LinkCreator
    {
        if (!$this->linkCreator) {
            $this->setLinkCreator(new LinkCreator);
        }
        if ($this instanceof GridInterface) {
            $this->getGrid()->setObjectDi($this->linkCreator);
        }
        return $this->linkCreator;
    }

    /**
     *
     * @param \Grid\Util\LinkCreator $creator
     */
    public function setLinkCreator(LinkCreator $creator)
    {
        $this->linkCreator = $creator;
    }
}