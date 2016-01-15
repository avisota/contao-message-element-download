<?php

/**
 * Avisota newsletter and mailing system
 * Copyright (C) 2013 Tristan Lins
 *
 * PHP version 5
 *
 * @copyright  bit3 UG 2013
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @package    avisota/contao-message-element-download
 * @license    LGPL-3.0+
 * @filesource
 */

namespace Avisota\Contao\Message\Element\Download;

use Avisota\Contao\Core\Message\Renderer;
use Avisota\Contao\Message\Core\Event\AvisotaMessageEvents;
use Avisota\Contao\Message\Core\Event\RenderMessageContentEvent;
use Contao\Doctrine\ORM\Entity;
use Contao\Doctrine\ORM\EntityAccessor;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DefaultRenderer
 *
 * @copyright  bit3 UG 2013
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @package    avisota/contao-message-element-download
 */
class DefaultRenderer implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     */
    static public function getSubscribedEvents()
    {
        return array(
            AvisotaMessageEvents::RENDER_MESSAGE_CONTENT => 'renderContent',
        );
    }

    /**
     * Render a single message content element.
     *
     * @param RenderMessageContentEvent $event
     *
     * @return string
     * @internal param MessageContent $content
     * @internal param RecipientInterface $recipient
     *
     */
    public function renderContent(RenderMessageContentEvent $event)
    {
        $content = $event->getMessageContent();

        if ($content->getType() != 'download' || $event->getRenderedContent()) {
            return;
        }

        /** @var EntityAccessor $entityAccessor */
        $entityAccessor = $GLOBALS['container']['doctrine.orm.entityAccessor'];

        $context                   = $entityAccessor->getProperties($content);
        $context['downloadSource'] = \Compat::resolveFile($context['downloadSource']);

        $file = new \File($context['downloadSource'], true);

        if (!$file->exists()) {
            return;
        }

        $context['downloadSize'] = \System::getReadableSize(filesize(TL_ROOT . DIRECTORY_SEPARATOR . $context['downloadSource']));
        $context['downloadIcon'] = 'assets/contao/images/' . $file->icon;

        if (empty($context['downloadTitle'])) {
            $context['downloadTitle'] = basename($context['downloadSource']);
        }

        $template = new \TwigTemplate('avisota/message/renderer/default/mce_download', 'html');
        $buffer   = $template->parse($context);

        $event->setRenderedContent($buffer);
    }
}