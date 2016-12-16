<?php
namespace Grid\Util\Traits;

use Grid\Util\LinkCreator;
use Grid\GridInterface;

use \Excepion;

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

        if (!$this instanceof GridInterface) {
            throw new Excepion('LinkCreatorAwareTrait must implement GridInterface');
        }

        $this->linkCreator->setGrid($this->getGrid());
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