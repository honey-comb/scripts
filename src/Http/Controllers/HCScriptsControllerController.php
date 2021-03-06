<?php
/**
 * @copyright 2018 innovationbase
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * Contact InnovationBase:
 * E-mail: hello@innovationbase.eu
 * https://innovationbase.eu
 */

namespace HoneyComb\Scripts\Http\Controllers;

use HoneyComb\Scripts\Helpers\HCScriptsHelper;
use HoneyComb\Scripts\Http\Resources\HCServiceResource;

class HCScriptsControllerController
{
    /**
     * @var HCScriptsHelper
     */
    private $helper;

    /**
     * @var HCServiceResource
     */
    private $config;

    /**
     * HCScriptsRoutesController constructor.
     * @param HCScriptsHelper $helper
     */
    public function __construct(HCScriptsHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param HCServiceResource $config
     * @throws \Exception
     */
    public function generate(HCServiceResource &$config)
    {
        $this->config = $config;

        $data = [
            'namespace' => $this->config->getPackageConfig()->getNamespaceForAdminController(),
            'serviceNs' => $this->config->getPackageConfig()->getNamespaceForService(),
            'serviceName' => $this->config->getServiceName(),
            'serviceNamespace' => $this->config->getPackageConfig()->getNamespaceForService(true),
            'translationLabel' => $this->config->getTranslation()->getLabelFieldForForm('page_title'),
            'urlName' => $this->config->getUrlName(),
            'formName' => $this->config->getRouteName(),
            'actionPrefix' => $this->config->getAclPrefix(),
            'columnList' => $this->generateColumnList(),
            'request' => $this->config->getServiceName() . 'Request',
            'requestNamespace' => $this->config->getPackageConfig()->getNameSpaceForRequest(true),
            'modelNamespace' => $this->config->getPackageConfig()->getNamespaceForModel(true),
        ];

        $data['create'] = $this->getCreateAction(['request' => $data['request']]);
        $data['update'] = $this->getUpdateAction(['request' => $data['request']]);
        $data['deleteSoft'] = $this->getDeleteSoftAction(['request' => $data['request']]);
        $data['restore'] = $this->getRestoreAction(['request' => $data['request']]);
        $data['deleteForce'] = $this->getDeleteForceAction(['request' => $data['request']]);

        $this->helper->createFileFromTemplate($this->config->getDirectory() . '/Http/Controllers/Admin/' . $this->config->getServiceName() . 'Controller.php','service/controller.hctpl', $data);
    }

    private function generateColumnList()
    {
        $model = $this->config->getModelConfig()->getDefaultModel();
        $this->config->getModelConfig()->getFieldsForModel($model);

        $fields = $model['fieldsModel'];
        $columns = '';

        foreach ($fields as $value)
        {
            $columns .= '\''. $value . '\' => $this->headerText(trans(\'' . $this->config->getTranslation()->getLabelFieldForForm($value) .'\')),';
        }

        return $columns;
    }

    /**
     * @param array $data
     * @return string
     */
    private function getCreateAction(array $data): string
    {
        $string = '';

        if (in_array('create', $this->config->getActions()->getAdmin()))
        {
            if ($this->config->isMultiLanguage()) {
                $template = 'service/controller/action.create.m.hctpl';
            } else {
                $template = 'service/controller/action.create.hctpl';
            }

            $string = $this->helper->createFileFromTemplate('', $template, $data, false);
        }

        return $string;
    }

    /**
     * @param array $data
     * @return string
     */
    private function getUpdateAction(array $data): string
    {
        $string = '';

        if (in_array('update', $this->config->getActions()->getAdmin()))
        {
            if ($this->config->isMultiLanguage()) {
                $template = 'service/controller/action.update.m.hctpl';
            } else {
                $template = 'service/controller/action.update.hctpl';
            }

            $string = $this->helper->createFileFromTemplate('', $template, $data, false);
        }

        return $string;
    }

    /**
     * @param array $data
     * @return string
     */
    private function getDeleteSoftAction(array $data): string
    {
        $string = '';

        if (in_array('delete', $this->config->getActions()->getAdmin()))
        {
            $template = 'service/controller/action.deleteSoft.hctpl';

            $string = $this->helper->createFileFromTemplate('', $template, $data, false);
        }

        return $string;
    }

    /**
     * @param array $data
     * @return string
     */
    private function getRestoreAction(array $data): string
    {
        $string = '';

        if (in_array('restore', $this->config->getActions()->getAdmin()))
        {
            $template = 'service/controller/action.restore.hctpl';

            $string = $this->helper->createFileFromTemplate('', $template, $data, false);
        }

        return $string;
    }

    /**
     * @param array $data
     * @return string
     */
    private function getDeleteForceAction(array $data): string
    {
        $string = '';

        if (in_array('delete_force', $this->config->getActions()->getAdmin()))
        {
            $template = 'service/controller/action.deleteForce.hctpl';

            $string = $this->helper->createFileFromTemplate('', $template, $data, false);
        }

        return $string;
    }
}
