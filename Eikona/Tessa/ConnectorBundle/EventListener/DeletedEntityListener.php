<?php
    /**
     * DeletedEntityListener.php
     *
     * @author    Matthias Mahler <m.mahler@eikona.de>
     * @copyright 2017 Eikona AG (http://www.eikona.de)
     */

    namespace Eikona\Tessa\ConnectorBundle\EventListener;

    use Pim\Component\Catalog\Model\Product;
    use Symfony\Component\EventDispatcher\GenericEvent;

    class DeletedEntityListener extends AbstractModificationListener
    {

        public function onPostRemove(GenericEvent $event)
        {
            $subject = $event->getSubject();

            $this->tessa->notifyAboutEntityDeletion($event->getSubjectId(), $subject);

        }

        public function onMassRemoveProducts(GenericEvent $event)
        {
            foreach ($event->getSubject() as $productId) {
                $this->tessa->notifyAboutEntityDeletion($productId, new Product());
            }
        }
    }