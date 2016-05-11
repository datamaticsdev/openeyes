<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */


namespace OEModule\OphCiExamination\controllers;


class ApiController extends \CController
{

    protected function getContentType()
    {
        return "application/xml";
    }

    protected function getFileContentFromXml($xml)
    {
        $doc = new \DOMDocument();
        $doc->loadXML($xml);

        // quick hack to get the content for now, we should validate the XML correctly.
        $nodes = $doc->getElementsByTagName('file');

        if ($nodes->length == 1) {
            return base64_decode($nodes->item(0)->nodeValue);
        }
        throw new \Exception("Unable to parse file content");
    }

    /**
     * Simple canvas retrieval method
     *
     * Not exactly API-ish here, but will do the job whilst we don't have external authorisation on defaultController.
     *
     * @param $uuid
     * @throws \Exception
     */
    public function actionDownloadCanvasForEditing($uuid)
    {
        $path = $this->getFilePathForUuid($uuid);
        if (!file_exists($path)) {
            throw new \Exception("Cannot find canvas file for uuid {$uuid}");
        }

        header('Content-Type: image/png');
        header('Content-Length: '.filesize($path));
        readfile($path);
    }

    /**
     * Note, doesn't currently perform auth checks for convenience for testing
     *
     * @TODO: implement auth check on this method - TBD what mechanism to use for this
     * @param $uuid
     * @throws \CHttpException
     * @throws \Exception
     */
    public function actionUploadEditedCanvas($uuid)
    {
        $path = $this->getFilePathForUuid($uuid);
        try {
            if (!file_exists($path)) {
                throw new \Exception("Cannot upload canvas file for non-existent uuid {$uuid}");
            }

            $edited_path = $this->getFilePathForUuid($uuid, true);
            if (file_exists($edited_path)) {
                throw new \Exception("Edited canvas already uploaded for uuid {$uuid}");
            }

            if ($content = $this->getFileContentFromXml(\Yii::app()->request->rawBody)) {
                file_put_contents($edited_path . '.lock', '1');
                file_put_contents($edited_path, $content);
                unlink($edited_path . '.lock');

                $this->sendResponse(200, "<canvasUpload><status>success</status></canvasUpload>");
            }
            throw new \Exception("Unknown error with processing");
        } catch (\Exception $e)
        {
            $this->sendResponse(400, "<canvasUpload><status>failure</status><message>" . $e->getMessage() . "</message></canvasUpload>");
        }
    }

    protected function sendResponse($status = 200, $body = '')
    {
        header('HTTP/1.1 ' . $status);
        header('Content-type: ' . $this->getContentType());
        if ($status == 401) header('WWW-Authenticate: Basic realm="OpenEyes"');

        if ($status == 405) header('Allow: POST');
        echo $body;
        \Yii::app()->end();
    }


    /**
     * Dubiously duplicated from DefaultController
     *
     * @TODO: stop hacking
     *
     * @param $uuid
     * @param bool $edited
     * @return string
     */
    private function getFilePathForUuid($uuid, $edited = false)
    {
        $path = \Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime" . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . "canvasEdits";
        return $path . DIRECTORY_SEPARATOR . $uuid . (($edited) ? '_edit' : '') . ".png";
    }

}