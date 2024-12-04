<?php

namespace App\Factory\Forms;

use App\Model\PrioritaModel;
use Nette\Application\UI\Form;
use Nette\Forms\Form as FormAlias;

class SlaAddFormFactory
{
    const int MONTHS = 12;
    const int DAYS = 31;
    const int HOURS = 23;
    const int MINUTES = 59;

    private FormFactory $formFactory;
    private PrioritaModel $prioritaModel;

    /**
     * @param FormFactory $formFactory
     * @param PrioritaModel $prioritaModel
     */
    public function __construct(
        FormFactory   $formFactory,
        PrioritaModel $prioritaModel
    )
    {
        $this->formFactory = $formFactory;
        $this->prioritaModel = $prioritaModel;
    }

    public function create(): Form
    {
        $form = $this->formFactory->create();

        $form->addHidden('tarif');
        $form->addText('nazev', 'Název tarifu')
            ->setDisabled();
        $priority = $this->prioritaModel->fetchPairs();
        // pomocny kontejner pro vypis hodnot ve formulari
        $hodnoty = $form->addContainer('hodnoty');
        // podle poctu priorit se vygeneruji dalsi hodnoty
        foreach ($priority as $key => $value) {
            $container = $hodnoty->addContainer($key);
            $container->addSelect('priorita', 'Priorita', $priority)
                ->setDefaultValue($key);
            $container->addText('cena_koeficient', 'Koeficient', null, 5)
                ->addRule(FormAlias::Filled)
                ->addRule(FormAlias::Float);
            $container->addSelect('mesic_reakce', 'Měsíců:', self::getTimeValue(self::MONTHS))
                ->addRule(FormAlias::Filled);
            $container->addSelect('den_reakce', 'Dnů', self::getTimeValue(self::DAYS))
                ->addRule(FormAlias::Filled);
            $container->addSelect('hodin_reakce', 'Hodin:', self::getTimeValue(self::HOURS))
                ->addRule(FormAlias::Filled);
            $container->addSelect('minut_reakce', 'Minut:', self::getTimeValue(self::MINUTES))
                ->addRule(FormAlias::Filled);
            $container->addSelect('mesic_vyhotoveni', 'Měsíců:', self::getTimeValue(self::MONTHS))
                ->addRule(FormAlias::Filled);
            $container->addSelect('den_vyhotoveni', 'Dnů', self::getTimeValue(self::DAYS))
                ->addRule(FormAlias::Filled);
            $container->addSelect('hodin_vyhotoveni', 'Hodin:', self::getTimeValue(self::HOURS))
                ->addRule(FormAlias::Filled);
            $container->addSelect('minut_vyhotoveni', 'Minut:', self::getTimeValue(self::MINUTES))
                ->addRule(FormAlias::Filled);
        }
        // Obrana před Cross-Site Request Forgery (CSRF)
        $form->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        // Tlacitko odeslat
        $form->addSubmit('btSbmt', 'Ulož');
        return $form;
    }

    /**
     * @param int $value
     * @return array
     */
    public static function getTimeValue(int $value = 59): array
    {
        $returnArray = array();
        for ($i = 0; $i <= $value; $i++) {
            $i < 10 ? $returnArray[$i] = '0' . $i : $returnArray[$i] = $i;
        }
        return $returnArray;
    }

}
