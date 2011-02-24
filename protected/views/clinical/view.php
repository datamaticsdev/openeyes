<?php

$this->renderPartial('base');

/**
 * Loop through all the element types completed for this event
 */
foreach ($elements as $element) {
	echo $this->renderPartial(
		'/elements/' .
			get_class($element['element']) .
			'/view/' .
			$element['siteElementType']->view_number,
		array('model' => $element)
	);
}
