<?php
/**
 * Created by PhpStorm.
 * User: nsotiropoulos
 * Date: 13/04/2018
 * Time: 15:55
 */

namespace App\Controller;

use Commercetools\Core\Client;
use Commercetools\Core\Model\Customer\CustomerReference;
use Commercetools\Core\Request\Products\ProductProjectionSearchRequest;
use Commercetools\Symfony\CtpBundle\Security\User\CtpUser;
use Commercetools\Symfony\CtpBundle\Security\User\UserProvider;
use Commercetools\Symfony\ShoppingListBundle\Model\Repository\ShoppingListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;


class ShoppingListController extends Controller
{
    /**
     * @var Client
     */
    private $client;

    private $repository;

    /**
     * ShoppingListController constructor.
     */
    public function __construct(Client $client, ShoppingListRepository $repository)
    {
        $this->client = $client;
        $this->repository = $repository;
    }

    public function index(Request $request, UserInterface $user)
    {

        $shoppingList = $this->repository->getAllShoppingListsByCustomer($request->getLocale(), CustomerReference::ofId($user->getId()));

        return $this->render('some.html.twig', ['product' => var_export($shoppingList->toArray(), true)]);
    }

    public function create()
    {
        $locale = $this->get('commercetools.locale.converter')->convert($request->getLocale());
        $shoppingList = $this->repository->createShoppingList($locale, CustomerReference::ofId($this->getCustomerId()));

        return $this->render('some.html.twig', ['product' => var_export($shoppingList->toArray(), true)]);
    }

    protected function getCustomerId()
    {
        $user = $this->getUser();
        if (is_null($user)) {
            return null;
        }
        $customerId = $user->getId();

        return $customerId;
    }
}