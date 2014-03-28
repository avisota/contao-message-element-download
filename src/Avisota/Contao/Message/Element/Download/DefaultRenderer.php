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
use Avisota\Contao\Entity\MessageContent;
use Avisota\Contao\Message\Core\Event\AvisotaMessageEvents;
use Avisota\Contao\Message\Core\Event\RenderMessageContentEvent;
use Avisota\Recipient\RecipientInterface;
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
	 * {@inheritdoc}
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
	 * @param MessageContent     $content
	 * @param RecipientInterface $recipient
	 *
	 * @return string
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
