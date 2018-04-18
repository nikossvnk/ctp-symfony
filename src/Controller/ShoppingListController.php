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
use Commercetools\Core\Request\ShoppingLists\Command\ShoppingListAddLineItemAction;
use Commercetools\Symfony\CtpBundle\Security\User\CtpUser;
use Commercetools\Symfony\CtpBundle\Security\User\UserProvider;
use Commercetools\Symfony\ShoppingListBundle\Manager\ShoppingListManager;
use Commercetools\Symfony\ShoppingListBundle\Model\Repository\ShoppingListRepository;
use Commercetools\Symfony\ShoppingListBundle\ShoppingListEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Commercetools\Symfony\ShoppingListBundle\Event\UpdateEvent;


class ShoppingListController extends Controller
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var ShoppingListManager
     */
    private $manager;

    /**
     * @var ShoppingListRepository
     */
    private $repository;

    /**
     * ShoppingListController constructor.
     */
    public function __construct(Client $client, ShoppingListManager $manager)
    {
        $this->client = $client;
        $this->manager = $manager;
    }

    public function index(Request $request, UserInterface $user)
    {
        $shoppingList = $this->manager->getAllOfCustomer($request->getLocale(), CustomerReference::ofId($user->getId()));

        return $this->render('shoppinglist.html.twig', ['product' => var_export($shoppingList->toArray(), true)]);
    }

    public function create(Request $request)
    {
        $this->repository->createShoppingList($request->getLocale(), CustomerReference::ofId($this->getCustomerId()), $request->request->get('_name'));

        return $this->redirectToRoute('shopping_list_index');
    }

    public function addLineItem(Request $request)
    {
        $this->manager->addLineItem(
            $this->manager->getById($request->getLocale(), $request->get('_shoppingListId')),
            $request->request->get('_productId'),
            (int)$request->request->get('_variantId'),
            1
        );

        return $this->redirectToRoute('shopping_list_index');
    }

    public function removeLineItem(Request $request)
    {
        $this->manager->removeLineItem(
            $this->manager->getById($request->getLocale(), $request->get('_shoppingListId')),
            $request->request->get('_lineItemId')
        );

        return $this->redirectToRoute('shopping_list_index');
    }

    public function changeLineItemQuantity(Request $request)
    {
        $this->manager->changeLineItemQuantity(
            $this->manager->getById($request->getLocale(), $request->get('_shoppingListId')),
            $request->request->get('_lineItemId'),
            (int)$request->request->get('_lineItemQuantity')
        );

        return $this->redirectToRoute('shopping_list_index');
    }

}