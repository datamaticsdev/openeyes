<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

?>

<div class="report-search">
    <form class="report-search-form mdl-color-text--grey-600" action="/report/reportData">
        <input type="hidden" name="report" value="<?= $report->getApp()->getRequest()->getQuery('report'); ?>" />
        <fieldset>
            <div class="mdl-selectfield">
                <label for="pcr-risk-mode">Rendering mode</label>
                <select name="mode" id="pcr-risk-mode" class="browser-default">
                    <?php foreach($modes as $mode):?>
                        <option value="<?=$mode['id']?>"><?=$mode['name']?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div>
                <button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored" type="submit" name="action">Submit
                    <i class="material-icons right">send</i>
                </button>
            </div>
        </fieldset>
    </form>
</div>