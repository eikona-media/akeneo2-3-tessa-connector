<?php
    /**
     * ProductModificationListener.php
     *
     * @author    Matthias Mahler <m.mahler@eikona.de>
     * @copyright 2017 Eikona AG (http://www.eikona.de)
     */

    namespace Eikona\Tessa\ConnectorBundle\EventListener;

use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Model\ProductModelInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class ProductModificationListener extends AbstractModificationListener
{

    public function onPostSave(GenericEvent $event)
    {
        $subject = $event->getSubject();

        if ($subject instanceof ProductInterface || $subject instanceof ProductModelInterface) {
            $this->tessa->notifyAboutProductModifications($subject);
        }


    }
}
