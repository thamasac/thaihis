<?php

namespace appxq\sdii\widgets;

use yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;
use appxq\sdii\assets\FroalaEditorAsset;
use yii\helpers\Url;

/**
 * Description of FroalaEditorWidget
 *
 * @author appxq
 */
class FroalaEditorWidget extends InputWidget {

    const PLUGIN_NAME = 'FroalaEditor';

    /**
     * @var array
     * Plugins to be included, leave empty to load all plugins
     * <pre>sample input:
     * [
     *      //specify only needed forala plugins (local files)
     *      'url',
     *      'align',
     *      'char_counter',
     *       ...
     *      //override default files for a specific plugin
     *      'table' => [
     *              'css' => '<new css file url>'
     *          ],
     *      //include custom plugin
     *      'my_plugin' => [
     *              'js' => '<js file url>' // required
     *              'css' => '<css file url>' // optional
     *          ],
     *      ...
     * ]
     */
    public $clientPlugins;

    /**
     * Remove these plugins from this list plugins, this option overrides 'clientPlugins'
     * @var array
     */
    public $excludedPlugins;

    /**
     * FroalaEditor Options
     * @var array
     */
    public $clientOptions = [];

    /**
     * csrf cookie param
     * @var string
     */
    public $csrfCookieParam = '_csrfCookie';

    /**
     * @var boolean
     */
    public $render = true;

    public $settings = [];
    
    public $toolbar_size = 'lg';
    /**
     * @inheritdoc
     */
    public function run() {
        if(!isset($this->clientOptions['key'])){
            $this->clientOptions['key'] = 'jG2G1A4D4D-17D2E2B1B2E4G1B3B8C7A6nc1QXIa2QZe1UOXATEX==';
        }
        if(!isset($this->clientOptions['toolbarButtons'])){
            if($this->toolbar_size == 'md'){
                $this->clientOptions['toolbarButtons'] = [
                    'html','bold', 'italic', 'underline', 'strikeThrough', 
                    '|', 
                    'fontFamily', 'fontSize', 'color', 
                    '|', 
                    'paragraphFormat', 'align', 'formatOL', 'formatUL', 'outdent', 'indent',
                    '-', 
                    'insertLink', 'insertImage', 'insertVideo', 'insertFile', 'insertTable',
                    '|', 
                    'undo', 'redo', 'fullscreen',
                ];
            } elseif($this->toolbar_size == 'sm'){
                $this->clientOptions['toolbarButtons'] = [
                    'html','bold', 'italic', 'underline',
                    '|', 
                    'fontFamily', 'fontSize', 'color', 
                    '|', 
                    'align', 'formatOL', 'formatUL', 'outdent', 'indent',
                    '|', 
                    'insertLink',
                ];
            } else {
                $this->clientOptions['toolbarButtons'] = [
                    'html', 'bold', 'italic', 'underline', 'strikeThrough', 
                    '|', 
                    'fontFamily', 'fontSize', 'color', 'inlineStyle', 'paragraphStyle', 
                    '|', 
                    'paragraphFormat', 'align', 'formatOL', 'formatUL', 'outdent', 'indent', 'quote', 
                    '-', 
                    'insertLink', 'insertImage', 'insertVideo', 'insertFile', 'embedly', 'insertTable', 
                    '|',
                    'emoticons', 'specialCharacters', 'subscript', 'superscript', 'insertHR', 'selectAll', 'clearFormatting', 
                    '|', 
                    'print', 'help',
                    '|', 
                    'undo', 'redo', 'fullscreen',
                ];
            }
        }
        if(isset($this->clientOptions['heightMin'])){
            $this->clientOptions['heightMin'] = (int)$this->clientOptions['heightMin'];
        }
        if(isset($this->clientOptions['heightMax'])){
            $this->clientOptions['heightMax'] = (int)$this->clientOptions['heightMax'];
        }
        if(!isset($this->clientOptions['zIndex'])){
            //$this->clientOptions['zIndex'] = 1050;
        }
        if(!isset($this->clientOptions['imageManagerLoadURL'])){
            $this->clientOptions['imageManagerLoadURL'] = Url::to(['/ezforms2/text-editor/images-get']);
        }
        if(!isset($this->clientOptions['imageManagerDeleteURL'])){
            $this->clientOptions['imageManagerDeleteURL'] = Url::to(['/ezforms2/text-editor/file-delete']);
        }
        if(!isset($this->clientOptions['imageUploadParam'])){
            $this->clientOptions['imageUploadParam'] = 'file';
        }
        if(!isset($this->clientOptions['imageMaxSize'])){
            $this->clientOptions['imageMaxSize'] = 100 * 1024 * 1024;
        }
        if(!isset($this->clientOptions['imageUploadURL'])){
            $this->clientOptions['imageUploadURL'] = Url::to(['/ezforms2/text-editor/image-upload-froala']);
        }
//        if(!isset($this->clientOptions['fileManagerLoadURL'])){
//            $this->clientOptions['fileManagerLoadURL'] = Url::to(['/ezforms2/text-editor/files-get']);
//        }
        if(!isset($this->clientOptions['fileUploadParam'])){
            $this->clientOptions['fileUploadParam'] = 'file';
        }
        if(!isset($this->clientOptions['fileMaxSize'])){
            $this->clientOptions['fileMaxSize'] = 100 * 1024 * 1024;
        }
        if(!isset($this->clientOptions['fileUploadURL'])){
            $this->clientOptions['fileUploadURL'] = Url::to(['/ezforms2/text-editor/file-upload-froala']);
        }
        if(!isset($this->clientOptions['videoUploadParam'])){
            $this->clientOptions['videoUploadParam'] = 'file';
        }
        if(!isset($this->clientOptions['imageMaxSize'])){
            $this->clientOptions['videoMaxSize'] = 100 * 1024 * 1024;
        }
        if(!isset($this->clientOptions['videoUploadURL'])){
            $this->clientOptions['videoUploadURL'] = Url::to(['/ezforms2/text-editor/file-upload-froala']);
        }
        
        if(!isset($this->clientOptions['language'])){
            $this->clientOptions['language'] = \backend\modules\ezforms2\classes\EzfFunc::getLanguage();
        }
        
//        if(!isset($this->clientOptions['enter'])){
//            $this->clientOptions['enter'] = null;
//        }
        
        if(!isset($this->clientOptions['htmlAllowedEmptyTags'])){
            $this->clientOptions['htmlAllowedEmptyTags'] = ['i', 'div', 'span', 'textarea', 'a', 'iframe', 'object', 'video', 'style', 'script', '.fa', '.fr-emoticon', '.fr-inner', 'path', 'line'];
        }
        
//        $this->clientOptions['htmlAllowedTags'] = ['.*'];
//        $this->clientOptions['htmlAllowedStyleProps'] = ['.*'];
//        $this->clientOptions['htmlAllowedAttrs'] = ['.*'];
//        $this->clientOptions['pasteAllowedStyleProps'] = ['.*'];
//        $this->clientOptions['htmlRemoveTags'] = ['script'];
//        $this->clientOptions['pasteDeniedAttrs'] = [];
//        $this->clientOptions['lineBreakerTags'] = [''];
//        $this->clientOptions['lineBreakerOffset'] = 0;
        
//        if(!isset($this->clientOptions['iframe'])){
//            $this->clientOptions['iframe'] = true;
//        }
        
        if ($this->render) {
            if ($this->hasModel()) {
                echo Html::activeTextarea($this->model, $this->attribute, $this->options);
            } else {
                echo Html::textarea($this->name, $this->value, $this->options);
            }
        }
        $this->registerClientScript();
    }

    /**
     * register client scripts(css, javascript)
     */
    public function registerClientScript() {
        $view = $this->getView();
        $asset = FroalaEditorAsset::register($view);
        $asset->registerClientPlugins($this->clientPlugins, $this->excludedPlugins);
        //theme
        $themeType = isset($this->clientOptions['theme']) ? $this->clientOptions['theme'] : 'default';
        if ($themeType != 'default') {
            $view->registerCssFile("{$asset->baseUrl}/css/themes/{$themeType}.css", ['depends' => '\appxq\sdii\assets\FroalaEditorAsset']);
        }
        //language
        $langType = isset($this->clientOptions['language']) ? $this->clientOptions['language'] : 'es_gb';
        if ($langType != 'es_gb') {
            if($langType=='en'){
                $langType='en_gb';
            }
            $view->registerJsFile("{$asset->baseUrl}/js/languages/{$langType}.js", ['depends' => '\appxq\sdii\assets\FroalaEditorAsset']);
        }
        $id = $this->options['id'];
        if (empty($this->clientPlugins)) {
            $pluginsEnabled = false;
        } else {
            $pluginsEnabled = array_diff($this->clientPlugins, $this->excludedPlugins ?: []);
        }
        if (!empty($pluginsEnabled)) {
            foreach ($pluginsEnabled as $key => $item) {
                $pluginsEnabled[$key] = lcfirst(yii\helpers\Inflector::camelize($item));
            }
        }
        $jsOptions = array_merge($this->clientOptions, $pluginsEnabled ? ['pluginsEnabled' => $pluginsEnabled] : []);
        $jsOptions = Json::encode($jsOptions);
        $view->registerJs("\$('#$id').froalaEditor($jsOptions);");
    }

}
