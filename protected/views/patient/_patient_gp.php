<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<section class="box patient-info js-toggle-container">
	<h3 class="box-title">General Medical Practitioner:</h3>
	<a href="#" class="toggle-trigger toggle-hide js-toggle">
		<span class="icon-showhide">
			Show/hide this section
		</span>
	</a>
	<div class="js-toggle-body">
		<div class="row data-row">
			<div class="large-4 column">
				<div class="data-label">Name:</div>
			</div>
			<div class="large-8 column">
				<div class="data-value"><?php echo ($this->patient->gp) ? $this->patient->gp->contact->fullName : 'Unknown'; ?></div>
			</div>
		</div>
		<div class="row data-row">
			<div class="large-4 column">
				<div class="data-label">Practice Address:</div>
			</div>
			<div class="large-8 column">
				<div class="data-value"><?php echo ($this->patient->practice && $this->patient->practice->contact->address) ? $this->patient->practice->contact->address->letterLine : 'Unknown'; ?></div>
			</div>
		</div>
		<div class="row data-row">
			<div class="large-4 column">
				<div class="data-label">Practice Telephone:</div>
			</div>
			<div class="large-8 column">
				<div class="data-value"><?php echo ($this->patient->practice && $this->patient->practice->phone) ? $this->patient->practice->phone : 'Unknown'; ?></div>
			</div>
		</div>
	</div>
</section>

<section class="box patient-info js-toggle-container">
	<h3 class="box-title">General Dental Practitioner:</h3>
	<a href="#" class="toggle-trigger toggle-hide js-toggle">
		<span class="icon-showhide">
			Show/hide this section
		</span>
	</a>
	<div class="js-toggle-body">
		<div class="row data-row">
			<div class="large-4 column">
				<div class="data-label">Name:</div>
			</div>
			<div class="large-8 column">
				<div class="data-value">Dr. Michael Smith</div>
			</div>
		</div>
		<div class="row data-row">
			<div class="large-4 column">
				<div class="data-label">Practice Address:</div>
			</div>
			<div class="large-8 column">
				<div class="data-value">23 Acacia Avenue, Newcastle Upon Tyne, NE2 6TF</div>
			</div>
		</div>
		<div class="row data-row">
			<div class="large-4 column">
				<div class="data-label">Practice Telephone:</div>
			</div>
			<div class="large-8 column">
				<div class="data-value">0191 133 5431</div>
			</div>
		</div>
	</div>
</section>
