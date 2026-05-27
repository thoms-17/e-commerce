<?php

namespace App\Controller\Admin;

use App\Enum\OrderStatus;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/orders', name: 'app_admin_order_')]
#[IsGranted('ROLE_ADMIN')]
class OrderController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(OrderRepository $repo): Response
    {
        return $this->render('admin/order/index.html.twig', [
            'orders'   => $repo->findBy([], ['createdAt' => 'DESC']),
            'statuses' => OrderStatus::cases(),
        ]);
    }

    #[Route('/{id}/status', name: 'status', methods: ['POST'])]
    public function updateStatus(int $id, Request $request, OrderRepository $repo, EntityManagerInterface $em): Response
    {
        $order = $repo->find($id);
        if (!$order) {
            throw $this->createNotFoundException();
        }

        if ($this->isCsrfTokenValid('order_status_' . $id, $request->request->get('_token'))) {
            $statusValue = $request->request->get('status');
            $status      = OrderStatus::tryFrom($statusValue);
            if ($status !== null) {
                $order->setStatus($status);
                $em->flush();
                $this->addFlash('success', sprintf('Commande #%d mise à jour : %s.', $order->getId(), $status->label()));
            }
        }

        return $this->redirectToRoute('app_admin_order_index');
    }
}
