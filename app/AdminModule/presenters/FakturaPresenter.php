<?php

/**
 * Description of FakturaPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Config\AppParameterService;
use App\Factory\Forms\InvoiceAddFormFactory;
use App\Factory\Forms\InvoiceEditFormFactory;
use App\Factory\Forms\SubscriberSupporterRelationFormFactory;
use App\Factory\Grids\FakturaDataGridFactory;
use App\Model\FakturaModel;
use App\Model\FakturaPolozkaModel;
use App\Model\FirmaModel;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\InvalidArgumentException;
use Nette\NotImplementedException;
use Nette\Utils\DateTime;
use Tracy\Debugger;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class FakturaPresenter extends AdminbasePresenter
{
    private FakturaModel $fakturaModel;
    private FakturaPolozkaModel $fakturaPolozkaModel;
    private FirmaModel $modelFirma;
    private AppParameterService $appParameterService;
    private InvoiceAddFormFactory $invoiceAddFormFactory;
    private InvoiceEditFormFactory $invoiceEditFormFactory;
    private SubscriberSupporterRelationFormFactory $subscriberSupporterRelationFormFactory;
    //TODO some mess in MojeFakturaControl, cannot load page when uncomment
//    private MojeFakturaControl $mojeFakturaControl;
    private FakturaDataGridFactory $gridFactory;

    public function __construct(
        FakturaModel                           $fakturaModel,
        FakturaPolozkaModel                    $fakturaPolozkaModel,
        FirmaModel                             $modelFirma,
        AppParameterService                    $appParameterService,
        InvoiceAddFormFactory                  $invoiceAddFormFactory,
        InvoiceEditFormFactory                 $invoiceEditFormFactory,
        SubscriberSupporterRelationFormFactory $subscriberSupporterRelationFormFactory,
        FakturaDataGridFactory                 $gridFactory
    )
    {
        parent::__construct();
        $this->fakturaModel = $fakturaModel;
        $this->fakturaPolozkaModel = $fakturaPolozkaModel;
        $this->modelFirma = $modelFirma;
        $this->appParameterService = $appParameterService;
        $this->invoiceAddFormFactory = $invoiceAddFormFactory;
        $this->invoiceEditFormFactory = $invoiceEditFormFactory;
        $this->subscriberSupporterRelationFormFactory = $subscriberSupporterRelationFormFactory;
//        $this->mojeFakturaControl = $mojeFakturaControl;
        $this->gridFactory = $gridFactory;
    }

    public function getAppParameterService(): AppParameterService
    {
        return $this->appParameterService;
    }

    /*************************************** DEFINE GRIDS *************************************
     * @throws DataGridException
     */

    protected function createComponentGrid(): DataGrid
    {
        return $this->gridFactory->create($this);
    }

    /**
     * @throws DataGridException
     */
    protected function createComponentGridPolozkyFaktury(): DataGrid
    {
        $invoiceId = $this->presenter->getParameter('id');
        return $this->gridFactory->createPolozkyFaktury($invoiceId);
//        return new PolozkyFakturyGrid($this->fakturaContext->table(FakturaPolozkaModel::TABLE_NAME));
    }

    /*************************************** PART ADD *************************************
     * @throws AbortException
     */

    public function renderAdd(int $odberatel, ?int $dodavatel)
    {
        try {
            //pokud neni nastaveny dodavatel nastavim jako dodavatele
            //firmu ve ktere je prihlaseny uzivatel.
            if (is_null($dodavatel)) {
                $dodavatel = $this->identity->data['firma'];
            }

            $faInicialy = $this->modelFirma->fetchDodavatelOdberatel($dodavatel, $odberatel);
            if (!$faInicialy) {
                throw new BadRequestException('Odberatel nebo dodavatel nebyl nalezen');
            }

            $this['add']->setDefaults($faInicialy);
        } catch (BadRequestException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('default');
        }
    }

    public function createComponentAdd(): Form
    {
        $form = $this->invoiceAddFormFactory->create();
        $form->onSuccess[] = [$this, 'add'];
        return $form;
    }

    public function add(Form $form)
    {
        try {
            $v = $form->getValues();
            $v->offsetSet('vytvoril', $this->identity->id);
            $v->offsetSet('datum_vystaveni', new DateTime);
            $v->offsetSet('datum_splatnosti', new DateTime(DateTime::from(DateTime::DAY * $v['splatnost'])));
            $v->offsetSet('ks', 3658);

            $this->fakturaModel->insert($v);
            $this->flashMessage('Nový záznam byl přidán');
            $this->redirect('default');
        } catch (Exception $exc) {
            Debugger::log($exc->getMessage());
            $form->addError('Nový záznam nebyl přidán');
        }
    }

    //zobrazi formular, ve kterem se bude moci vybrat odberatel a dodavatel
    public function renderAddSelect()
    {
        throw new NotImplementedException("New invoice is not implement yet");
    }


    public function createComponentOdberatelDodavatel(): Form
    {
        $form = $this->subscriberSupporterRelationFormFactory->create();
        $form->onSuccess[] = [$this, 'edit'];
        return $form;
    }

    /**
     * @param Form $form
     */
    public function HandleOdberatelDodavatel(Form $form)
    {
        throw new NotImplementedException();
    }

    /*************************************** PART EDIT **************************************/

    /**
     * Formular pro editaci faktury
     * @return Form
     */
    public function createComponentEdit(): Form
    {
        $form = $this->invoiceEditFormFactory->create();
        $form->onSuccess[] = [$this, 'edit'];
        return $form;
    }

    /**
     * @param int $id Identifikator faktury
     * @throws AbortException
     */
    public function renderEdit(int $id)
    {
        try {
            #$this->setView('../_edit');
            //nactu hodnoty pro editaci, pritom overim jestli hodnoty existuji
            $v = $this->fakturaModel->fetchById($id);
            $this->getTemplate()->title = $v['vs'];
            $this->getTemplate()->faktura = $id;

            //podminka pro zobrazeni polozek pro konkretni fakturu
            $this->fakturaPolozkaModel->fetchAllByIdFaktura($id);

            //odeberu idecko z pole
//            $v->offsetUnset('id');

            //upravene hodnoty odeslu do formulare
            $this['edit']->setDefaults(array('id' => $id, 'new' => $v));
        } catch (BadRequestException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('default');
        }
    }


    /**
     * Zpracovani formulare po editaci
     * @throws AbortException
     */
    public function edit(Form $form)
    {
        try {
            $v = $form->getValues();
            $this->fakturaModel->updateItem($v, $v['id']);
        } catch (Exception $exc) {
            Debugger::log($exc->getMessage());
            $form->addError('Záznam nebyl změněn');
        }
        $this->flashMessage('Záznam byl úspěšně změněn');
        $this->redirect('default');
    }

    /*************************************** PART GENERATE PDF **************************************/

    /**
     * Funkce generuje PDF soubor faktury
     * @param int $id identifikator faktury
     */
    public function actionGeneratePdf(int $id)
    {
        throw new NotImplementedException();

//        $this->mojeFakturaControl->exportToPdf($id);
//        try {
        // nazev souboru faktury
//            $faData->offsetSet('pdf_soubor', $faData['vs'] . '.pdf');

//            $this['fa']->exportToPdf($mpdf, __DIR__ . '/../../../facka/' . $faData['pdf_soubor'], "F");

        #$this->redirectUrl(__DIR__ . '/../../../facka/'.$faData['vs'].'.pdf');

        //nastavim u faktury nazev PDF souboru
//            $arr = new ArrayHash;
//            $arr->offsetSet('pdf_so/ubor', $faData['pdf_soubor']);

//            $this->fakturaModel->updateItem($arr, $id);
//            unset($arr);
//            $this->flashMessage('PDF faktura byla vygenerovana');
//            $this->redirect('default');


//        } catch (InvalidArgumentException $exc) {
//            $this->flashMessage($exc->getMessage());
//            $this->redirect('default');
//        }

    }


//    public function actionGeneratePdfEciovni($id)
//    {
//        try {
//            $v = $this->fakturaModel->fetch($id);
//
//            $dateNow = new $v['datum_vystaveni'];
//            $dateExp = new $v['datum_splatnosti'];
//
//            // TODO: change to parameter
//            $dateExp->modify('+14 days');
//            $variableSymbol = $v['vs'];
//
//            $supplierBuilder = new ParticipantBuilder($v['dodavatel_nazev'], $v['dodavatel_ulice'], null, // cislo popisne - mam v ramci ulice
//                $v['dodavatel_obec'], $v['dodavatel_psc']);
//            $supplier = $supplierBuilder->setIn($v['dodavatel_ico'])
//                ->setTin($v['dodavatel_dic'])
//                ->setAccountNumber($v['dodavatel_cislo_uctu'])
//                ->build();
//
//            $customerBuilder = new ParticipantBuilder($v['odberatel_nazev'], $v['odberatel_ulice'], null, // cislo popisne - mam v ramci ulice
//                $v['odberatel_obec'], $v['odberatel_psc']);
//            $customer = $customerBuilder->setIn($v['odberatel_ico'])
//                ->setAccountNumber($v['odberatel_cislo_uctu'])
//                ->setTin($v['odberatel_dic'])
//                ->build();
//
//            $items = array();
//            foreach ($this->fakturaPolozkaModel->fetchAllByIdFaktura($id) as $item):
//                $items[] = new ItemImpl($item['nazev'], $item['pocet_polozek'], $item['cena'], TaxImpl::fromPercent($item['procent']));
//            endforeach;
//
//            /* $items = array(
//              new ItemImpl('Testing item - from percent', 1, 900, TaxImpl::fromPercent(22)),
//              new ItemImpl('Testing item - from lower decimal', 1, 900, TaxImpl::fromLowerDecimal(0.22)),
//              new ItemImpl('Testing item - from upper decimal', 1, 900, TaxImpl::fromUpperDecimal(1.22)),
//              ); */
//
//            $dataBuilder = new DataBuilder(date('YmdHis'), 'Invoice - invoice number', $supplier, $customer, $dateExp, $dateNow, $items);
//            $dataBuilder->setVariableSymbol($variableSymbol)
//                ->setDateOfVatRevenueRecognition($dateNow);
//            $data = $dataBuilder->build();
//
//            // in case you downloaded mPDF separately
//            include_once(__DIR__ . '/../../vendor/others/mpdf/mpdf.php');
//
//            $mpdf = new \mPDF('utf-8');
//
//            // Exporting prepared invoice to PDF.
//            // To save the invoice into a file just use the second and the third parameter, equally
//            // as it's described in the documentation of mPDF->Output().
//
//            $this['eciovni'] = new Eciovni($data);
//            $this['eciovni']->setTemplatePath(__DIR__ . '/../templates/Faktura/fakturaBezDph.latte');
//
//            $this['eciovni']->exportToPdf($mpdf);
//
//        } catch (InvalidArgumentException $exc) {
//            $this->flashMessage($exc->getMessage());
//            $this->redirect('default');
//        }
//
//    }

    /**
     * Cast DROP
     * @param int $id Identifikator polozky
     * @throws AbortException
     */
    public function actionDrop(int $id)
    {
        try {
            $this->fakturaModel->remove($id);
            $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
            $this->redirect('Faktura:default');
        } catch (InvalidArgumentException $exc) {
            Debugger::log($exc->getMessage());
            $this->flashMessage($exc->getMessage());
            $this->redirect('Faktura:default'); //change it !!!
        }

    }

}
