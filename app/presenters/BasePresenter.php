<?php

namespace App\Presenters;

use ArrayIterator;
use Components\Navigation\Navigation;
use CssMin;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\Presenter;
use Nette\Forms\Controls\CsrfProtection;
use Nette\Forms\Controls\SelectBox;
use Nette\Forms\Controls\SubmitButton;
use Vodacek\Forms\Controls\DateInput;
use WebLoader\Compiler;
use WebLoader\FileCollection;
use WebLoader\InvalidArgumentException;
use WebLoader\Nette\CssLoader;
use WebLoader\Nette\JavaScriptLoader;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Presenter
{
    /** @var string Obsahuje stejnou cestu jako basePath v sablone */
    private $basePath;

    /** @var FileCollection Description */
    protected $cssFiles;

    /** @var FileCollection */
    protected $jsFiles;

    protected $context;

    protected function startup()
    {
        $this->context = $this->getContext();
        //WebLoader nastavim cesty k css souborum
        $this->cssFiles = new FileCollection($this->context->getParameters()['wwwDir'] . '/css');

        //WebLoader nastavim cesty k css souborum
        $this->jsFiles = new FileCollection($this->context->getParameters()['wwwDir'] . '/js');
        parent::startup();
    }

    public function beforeRender()
    {
        $this->getSession()->isStarted();
        parent::beforeRender();
        if ($this->getSession()->isStarted()) {
            $this->getSession()->start();
        }
        //   nastavim basePath
        $this->basePath = $this->context->getParameters()['wwwDir'];
        DateInput::register();
    }

//    protected function createTemplate($class = null)
//    {
//        /**
//         *  Texy pro vypis work logu
//         */
//        $texy = new Texy;
//        $texy->encoding = 'utf-8';
//        $texy->imageModule->linkedRoot = $this->context->getParameters()['wwwDir'];
//        $texy->imageModule->root = $this->basePath . '/images/';
//
//        $texy->setOutputMode(Texy::XHTML1_TRANSITIONAL);
//        $texy->allowed['heading'] = FALSE;
//        $texy->allowed['paragraph'] = FALSE;
//
//        //registrace filtru
//        $template = parent::createTemplate($class); //parent::createTemplate($class);
//        $template->registerHelper('texyWl', callback($texy, 'process'));
//        $template->registerHelper('texy', callback($texy, 'process'));
//        return $template;
//    }

    /**
     * Nacteni css
     * @throws InvalidArgumentException
     */
    protected function createComponentCss()
    {
        $this->cssFiles->addRemoteFiles([
            'https://code.jquery.com/ui/1.11.0/themes/black-tie/jquery-ui.css',
        ]);
        $this->cssFiles->addFiles([
            'NiftyGrid/grid.css',
            'NiftyGrid/example.css',
        ]);

        $compiler = Compiler::createCssCompiler(
            $this->cssFiles,
            $this->context->getParameters()['wwwDir'] . '/webtemp'
        );
        $compiler->addFilter(function ($code) {
            return CssMin::minify($code);
        });
        return new CssLoader($compiler, $this->template->basePath . '/webtemp');
    }


    public function createComponentJs()
    {
        $this->jsFiles->addRemoteFiles([
            'https://jush.sourceforge.io/jush.js',
            'https://code.jquery.com/jquery-1.11.1.min.js',
            'https://code.jquery.com/ui/1.11.1/jquery-ui.min.js',
        ]);
        $this->jsFiles->addFiles([
            'netteForms.js',
            'dateInput.js',
            'datePickerEnable.js',
            'main.js',
            'jushConfig.js',
            'NiftyGrid/grid.js',
        ]);

        $compiler = Compiler::createJsCompiler(
            $this->jsFiles,
            $this->context->getParameters()['wwwDir'] . '/webtemp'
        );
        return new JavaScriptLoader($compiler, $this->template->basePath . '/webtemp');
    }

    /**
     * Componenta, ktera zobrazuje hlavni menu
     * @return Navigation Funkce vraci hlavni menu
     * @throws InvalidLinkException
     */
    protected function createComponentHlavniMenu()
    {
        $menu = new Navigation();
        $menu->add('Domu', $this->link('Homepage:'));
        $menu->add('Prihlas se', $this->link('Sign:in'));
        return $menu;
    }

    /**
     * Funkce vytvori log string, ktery se pak zapise do databaze
     * @param ArrayIterator $components Komponenty z odeslaneho formulare
     * @return string string pripraveny k ulozeni do databaze
     */
    protected function createLog($components)
    {
        //pripravim si obsah logu pro ulozeni do databaze
        $ci_log = '';

        foreach ($components as $value) {
            //pokud je komponenta tlacitko nebo ochranny token tak to vyrad
            if ($value instanceof SubmitButton || $value instanceof CsrfProtection) {
                continue;
            }
            //pokud je hodnota SelectBox musim nacist hodnoty
            if ($value instanceof SelectBox) {
                //identifikator vybraneho objektu
                $i = $value->value;

                //pokud neni null
                if (!is_null($i)) {
                    $ci_log .= '**' . $value->caption . '**: ' . $value->items[$i] . ' <br />';
                }
            } else {
                $ci_log .= $value->caption . ' ' . $value->value . '<br />';
            }
        }
        return $ci_log;
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }


}
