<?php
declare(strict_types=1);

namespace App\Controller;

use Commercetools\Core\Model\Customer\CustomerReference;
use Commercetools\Core\Request\Products\ProductProjectionSearchRequest;
use Commercetools\Core\Request\ShoppingLists\Command\ShoppingListAddLineItemAction;
use Commercetools\Symfony\CtpBundle\Security\User\CtpUser;
use Commercetools\Symfony\CtpBundle\Security\User\UserProvider;
use Commercetools\Symfony\ShoppingListBundle\Manager\ShoppingListManager;
use Commercetools\Symfony\ShoppingListBundle\Model\Repository\ShoppingListRepository;
use Commercetools\Symfony\ShoppingListBundle\ShoppingListEvents;
use GuzzleHttp\Client;
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
     * ShoppingListController constructor.
     */
    public function __construct(ShoppingListManager $manager)
    {
        $this->manager = $manager;
    }

    public function index(Request $request, UserInterface $user)
    {
        $shoppingList = $this->manager->getAllOfCustomer($request->getLocale(), CustomerReference::ofId($user->getId()));

        return $this->render('shoppinglist.html.twig', ['product' => var_export($shoppingList->toArray(), true)]);
    }

    public function create(Request $request, UserInterface $user)
    {
        $this->manager->createShoppingList($request->getLocale(), CustomerReference::ofId($user->getId()), $request->request->get('_name'));

        return $this->redirectToRoute('shopping_list_index');
    }

//    public function modifyList(Request $request)
//    {
//        $update = $this->manager->update($this->manager->getById($request->getLocale(), $request->get('_shoppingListId')));
//        $update->addLineItem(function(ShoppingListAddLineItemAction $action): ShoppingListAddLineItemAction {
//            // modify action as needed
//            return $action;
//        });
//        $update->changeName($request->get('name'));
//        $update->changeKey($request->get('key'));
//
//        $update->flush();
//    }

    public function addLineItem(Request $request)
    {
        $shoppingList = $this->manager->getById($request->getLocale(), $request->get('_shoppingListId'));


        $updateBuilder = $this->manager->update($shoppingList);
        $updateBuilder->addLineItem(function (ShoppingListAddLineItemAction $action) use($request): ShoppingListAddLineItemAction {
            $action->setProductId($request->get('_productId'));
            $action->setVariantId($request->get('_variantId'));
            $action->setQuantity(1);
            return $action;
        });

        $updateBuilder->flush();

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
