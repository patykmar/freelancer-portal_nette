<?php

namespace App\Form\Admin\Add;

/**
 * Description of SlaForm
 *
 * @author Martin Patyk
 */

use App\Model\PrioritaModel;
use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Form;

class SlaForm extends UIForm
{
    const MONTHS = 12;
    const DAYS = 31;
    const HOURS = 23;
    const MINUTES = 59;

    public function __construct(IContainer $parent = NULL, $name = NULL)
    {
        parent::__construct($parent, $name);
        $this->addHidden('tarif');
        $this->addText('nazev', 'Název tarifu')
            ->setDisabled();
        $priority = PrioritaModel::fetchPairs();
        //	pomocny kontejner pro vypis hodnot ve formulari
        $hodnoty = $this->addContainer('hodnoty');
        //	podle poctu priorit se vygeneruji dalsi hodnoty
        foreach ($priority as $key => $value):
            $hodnoty->addContainer($key);
            $hodnoty[$key]->addSelect('priorita', 'Priorita', $priority)
                ->setDefaultValue($key);
            $hodnoty[$key]->addText('cena_koeficient', 'Koeficient', NULL, 5)
                ->addRule(Form::FILLED)
                ->addRule(Form::FLOAT);
            $hodnoty[$key]->addSelect('mesic_reakce', 'Měsíců:', SlaForm::getTimeValue(self::MONTHS))
                ->addRule(Form::FILLED);
            $hodnoty[$key]->addSelect('den_reakce', 'Dnů', SlaForm::getTimeValue(self::DAYS))
                ->addRule(Form::FILLED);
            $hodnoty[$key]->addSelect('hodin_reakce', 'Hodin:', SlaForm::getTimeValue(self::HOURS))
                ->addRule(Form::FILLED);
            $hodnoty[$key]->addSelect('minut_reakce', 'Minut:', SlaForm::getTimeValue(self::MINUTES))
                ->addRule(Form::FILLED);
            $hodnoty[$key]->addSelect('mesic_vyhotoveni', 'Měsíců:', SlaForm::getTimeValue(self::MONTHS))
                ->addRule(Form::FILLED);
            $hodnoty[$key]->addSelect('den_vyhotoveni', 'Dnů', SlaForm::getTimeValue(self::DAYS))
                ->addRule(Form::FILLED);
            $hodnoty[$key]->addSelect('hodin_vyhotoveni', 'Hodin:', SlaForm::getTimeValue(self::HOURS))
                ->addRule(Form::FILLED);
            $hodnoty[$key]->addSelect('minut_vyhotoveni', 'Minut:', SlaForm::getTimeValue(self::MINUTES))
                ->addRule(Form::FILLED);
        endforeach;
        //	Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        //	Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }

    /**
     * @param int $value
     * @return array
     */
    public static function getTimeValue($value = 59)
    {
        $returnArray = array();
        for ($i = 0; $i <= $value; $i++) {
            $i < 10 ? $returnArray[$i] = '0' . $i : $returnArray[$i] = $i;
        }
        return $returnArray;
    }

}