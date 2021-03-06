<?php

/**
 * Avisota newsletter and mailing system
 * Copyright © 2016 Sven Baumann
 *
 * PHP version 5
 *
 * @copyright  way.vision 2016
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @package    avisota/contao-message-element-download
 * @license    LGPL-3.0+
 * @filesource
 */

/**
 * Table orm_avisota_message_content
 * Entity Avisota\Contao:MessageContent
 */
$GLOBALS['TL_DCA']['orm_avisota_message_content']['metapalettes']['download'] = array
(
    'type'      => array('cell', 'type', 'headline'),
    'download'  => array('downloadSource', 'downloadTitle'),
    'expert'    => array(':hide', 'cssID', 'space'),
    'published' => array('invisible'),
);

$GLOBALS['TL_DCA']['orm_avisota_message_content']['fields']['downloadSource'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['downloadSource'],
    'exclude'   => true,
    'inputType' => 'fileTree',
    'eval'      => array('fieldType' => 'radio', 'files' => true, 'mandatory' => true, 'tl_class' => 'clr')
);

$GLOBALS['TL_DCA']['orm_avisota_message_content']['fields']['downloadTitle'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['downloadTitle'],
    'exclude'   => true,
    'search'    => true,
    'inputType' => 'text',
    'eval'      => array('maxlength' => 255)
);
