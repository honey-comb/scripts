<?php

namespace HoneyComb\Scripts\DTO;

use HoneyComb\Core\DTO\HCBaseDTO;
use HoneyComb\Scripts\Helpers\HCScriptsHelper;

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
        foreach ($this->translations as $key => $translation)
        {
            $translations = "";

            foreach ($translation as $transKey => $value)
                $translations .= '"' . $transKey . '" => "' . $value . '",';

            $this->helper->createFileFromTemplate($this->getTranslationDestination($key), "service/translations.hctpl", ["translations" => $translations]);
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
    public function getTranslationDestination (string $lang): string
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

        return$this->translationLocation . $this->translationPrefix . '.' . $name;
    }

    /**
     * @return array
     */
    protected function jsonData(): array
    {
        return get_object_vars($this);
    }
}