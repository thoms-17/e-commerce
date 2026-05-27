<?php

namespace App\Controller\Admin;

use App\Repository\CategoryRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'app_admin_')]
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractController
{
    #[Route('', name: 'dashboard', methods: ['GET'])]
    public function index(
        ProductRepository  $productRepo,
        CategoryRepository $categoryRepo,
        OrderRepository    $orderRepo,
        UserRepository     $userRepo,
    ): Response {
        return $this->render('admin/dashboard.html.twig', [
            'totalProducts'  => $productRepo->count([]),
            'totalCategories'=> $categoryRepo->count([]),
            'totalOrders'    => $orderRepo->count([]),
            'totalUsers'     => $userRepo->count([]),
            'totalRevenue'   => $orderRepo->getTotalRevenue(),
            'latestOrders'   => $orderRepo->findBy([], ['createdAt' => 'DESC'], 5),
        ]);
    }
}
