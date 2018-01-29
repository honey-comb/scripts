<?php
/**
 * @copyright 2018 interactivesolutions
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
 * Contact InteractiveSolutions:
 * E-mail: info@interactivesolutions.lt
 * http://www.interactivesolutions.lt
 */
declare(strict_types = 1);

namespace HoneyComb\Scripts\DTO;

use HoneyComb\Scripts\Helpers\HCScriptsHelper;
use HoneyComb\Starter\DTO\HCBaseDTO;

/**
 * Class HCTranslationsDTO
 * @package HoneyComb\Scripts\DTO
 */
class HCTranslationsDTO extends HCBaseDTO
{

    /**
     * Translation prefix (filename)
     * @var HCScriptsHelper
     */

    /**
     * @var string
     */
    private $translationPrefix;

    /**
     * @var HCScriptsHelper
     */
    private $helper;

    /**
     * @var string
     */
    private $rootDirectory;

    /**
     * @var array
     */
    private $models;

    /**
     * Translations list
     * @var array
     */
    private $translations;

    /**
     * Package location
     * @var string
     */
    private $translationLocation;

    /**
     * HCServiceDTO constructor.
     * @param HCServiceModelsDTO $models
     * @param HCScriptsHelper $helper
     */
    public function __construct(HCServiceModelsDTO $models, HCScriptsHelper $helper)
    {
        $this->helper = $helper;
        $this->models = $models;
    }

    /**
     * Adding service translations
     * @param string $key
     * @return mixed
     */
    private function getTranslation(string $key)
    {
        //TODO: multi language (read from db, take available languages and connect with google translate API)
        $language = app()->getLocale();
        $translation = str_replace("_", " ", ucfirst($key));

        $this->translations[$language][$key] = $translation;

        return $translation;
    }

    /**
     * Generating translations
     */
    public function generate()
    {
        foreach ($this->translations as $key => $translation) {
            $translations = "";

            foreach ($translation as $transKey => $value) {
                $translations .= '"' . $transKey . '" => "' . $value . '",';
            }

            $this->helper->createFileFromTemplate($this->getTranslationDestination($key), "service/translations.hctpl",
                ["translations" => $translations]);
        }
    }

    /**
     * setting translation prefix
     * @param string $url
     */
    public function setTranslationPrefix(string $url)
    {
        $this->translationPrefix = $this->helper->stringWithUnderscore(strtolower($url));
    }

    /**
     * Setting root directory
     *
     * @param string $packageName
     * @param string $directory
     */
    public function setRootDirectory(string $packageName, string $directory)
    {
        $this->rootDirectory = $directory . 'resources/lang/';
        $this->translationLocation = $directory == "" ? "" : $packageName . '::';
    }

    /**
     * Getting translation file location
     *
     * @param string $lang
     * @return string
     */
    public function getTranslationDestination(string $lang): string
    {
        return $this->rootDirectory . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . $this->translationPrefix . '.php';
    }

    /**
     * @param $name
     * @return string
     */
    public function getLabelFieldForForm($name)
    {
        $this->getTranslation($name);

        return $this->translationLocation . $this->translationPrefix . '.' . $name;
    }

    /**
     * @return array
     */
    protected function jsonData(): array
    {
        return get_object_vars($this);
    }
}
