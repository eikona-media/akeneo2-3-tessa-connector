<?php
    /**
     * CategoryModificationListener.php
 *
     * @author    Matthias Mahler <m.mahler@eikona.de>
     * @copyright 2017 Eikona AG (http://www.eikona.de)
 */

    namespace Eikona\Tessa\ConnectorBundle\EventListener;

use Akeneo\Component\Classification\Model\CategoryInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class CategoryModificationListener extends AbstractModificationListener
{

    public function onPostSave(GenericEvent $event)
    {
        $subject = $event->getSubject();

        if ($subject instanceof CategoryInterface) {
            $this->tessa->notifyAboutCategoryModifications($subject);
        }
    }

}