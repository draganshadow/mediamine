<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace MediaMine\Controller;
use Zend\Debug\Debug;
use MediaMine\Parser\MovieParser;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Dom\Query;
use MediaMine\Initializer\EntityManagerAware;
use MediaMine\Initializer\ElasticsearchAware;
use MediaMine\Entity\User;


class DebugController extends AbstractController
{
    public function indexAction()
    {
//        $url = 'http://php.net/manual/en/class.domelement.php';
//        $client = new Zend_Http_Client($url);
//        $response = $client->request();
//        $html = $response->getBody();
        $html = file_get_contents('./data/test/allocine/brucewillis.html');
//        $html = file_get_contents('./data/test/allocine/fayedunaway.html');
        $dom = new Query($html);
        //requete de type selecteur css.
//        $result = $dom->execute('ul li');
        //requete xpath
//        $result = $dom->queryXpath("//li//*[contains(text(), 'Âge')]");
//        Debug::dump($result);
        //afficher le html
//        Debug::dump($result->current()->C14N());
        //Recupere le parent et afficher html
//        Debug::dump($result->current()->parentNode->C14N());
        //recuperer le frere et afficher le contenu texte
//        Debug::dump($result->current()->nextSibling->nodeValue);

        //La requete idéale

        $age = $dom->queryXpath("//*[contains(text(), 'Âge')]/../*[@class='oflow_a']");
        //Recupere le parent et afficher html
//        Debug::dump($age->current()->nodeValue);
        //Et merci php
//        Debug::dump(intval($age->current()->nodeValue));

        $age = intval($age->current()->nodeValue);


        $nationality = $dom->queryXpath("//*[contains(text(), 'Nationalité')]/../*[@class='oflow_a']");
        $nationality = $nationality->current()->nodeValue;

        $birthday = $dom->queryXpath("//*[contains(text(), 'Naissance')]/../*[@class='oflow_a']");
        $birthday = $birthday->current()->nodeValue;

        $participation = $dom->queryXpath("//h2[contains(text(), 'Sa biographie')]/../following-sibling::div[@class='margin_20b']//div[@class='bio_participations_inner']");
        $participation = $participation->current()->nodeValue;

        $biography = $dom->queryXpath("//h2[contains(text(), 'Sa biographie')]/../following-sibling::div[@class='margin_20b']");
        var_dump($biography->current()->C14N());
        $biography = $biography->current()->nodeValue;
        $biography = $dom->queryXpath("//h2[contains(text(), 'Sa biographie')]/../following-sibling::div[@class='margin_20b']/following-sibling::div[1]");
        var_dump($biography->current()->C14N());
        $biography = $biography->current()->nodeValue;
//        $biography = str_replace($participation, '', $biography);


        $firstmovies = $dom->queryXpath("//h3[contains(text(), 'Ses premiers pas')]/..//following-sibling::ul[@class='list_li_m_40b align_c']");
        $firstmovies = $firstmovies->current()->nodeValue;


        //$biography2 = preg_replace('/^(.*)série?/', ' ', $biography);
        //$role = preg_match('#^(.*)série.#', $biography, $match);

        echo($age);
        echo '<br>';
        echo($nationality);
        echo '<br>';
        echo($birthday);
        echo '<br>';
        echo($biography);
        echo '<br>';
        echo($participation);
        echo '<br><br>';
        echo($firstmovies);
        echo '<br>';





        //     $viewModel = new ViewModel();
   //     $viewModel->setTerminal(true);
   //     return $viewModel;


    }

}