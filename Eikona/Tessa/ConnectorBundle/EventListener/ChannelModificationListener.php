<?php
    /**
     * ChannelModificationListener.php
     *
     * @author    Matthias Mahler <m.mahler@eikona.de>
     * @copyright 2017 Eikona AG (http://www.eikona.de)
     */

    namespace Eikona\Tessa\ConnectorBundle\EventListener;

    use Pim\Component\Catalog\Model\ChannelInterface;
    use Symfony\Component\EventDispatcher\GenericEvent;

    class ChannelModificationListener extends AbstractModificationListener
    {

        public function onPostSave(GenericEvent $event)
        {
            $subject = $event->getSubject();

            if ($subject instanceof ChannelInterface) {
                $this->tessa->notifyAboutChannelModifications($subject);
            }
        }

    }