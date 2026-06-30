<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class KitchenChefController extends Controller
{
    /**
     * Display the Kitchen Display System (KDS) Tablet.
     */
    public function kds()
    {
        // Sample orders matching the user mockup
        $initialOrders = [
            [
                'id' => '#402-A',
                'customer' => 'Table 14 — Desta',
                'elapsed_seconds' => 845, // 14:05
                'instruction' => 'Allergic to Peanut Oil',
                'status' => 'Preparing', // New, Preparing, Ready
                'items' => [
                    [
                        'name' => 'Doro Wat (Spicy Chicken)',
                        'quantity' => 2,
                        'completed' => true,
                        'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDzZYcxiwtS3oy6TK7Ve1OjWNDxf1zQl5a2lXiPzO3ppiiWGeJUnNRVAaTOuYWXvQt1OD322UoeC0jsNo32_zDIcMOkxkiii4ETJNdR1VzgEbIKpFEdFI1HZFiaHrJw78v5FFAR9gHLEUavAI4s2ZUAzFD-HOaxUyHTqbCR2AEtx9l2TLdjpIkTk7ckIsMVSTzB3zdSnf4j23b9Kli-l3iBggGj5mgDpKPGSoM9yeyenUoZzytqf8y2fvMDlkP3trk_JYMDYHYzupk',
                    ],
                    [
                        'name' => 'Special Kitfo (Rare)',
                        'quantity' => 1,
                        'completed' => false,
                        'image' => null,
                    ],
                    [
                        'name' => 'Veggie Combo (Beyaynetu)',
                        'quantity' => 1,
                        'completed' => false,
                        'image' => null,
                    ]
                ],
                'note' => null,
            ],
            [
                'id' => '#405-C',
                'customer' => 'Pick-up — Kebede',
                'elapsed_seconds' => 138, // 02:18
                'instruction' => null,
                'status' => 'New',
                'items' => [
                    [
                        'name' => 'Meat Sambusas',
                        'quantity' => 4,
                        'completed' => false,
                        'image' => null,
                    ],
                    [
                        'name' => 'Shiro Wat',
                        'quantity' => 2,
                        'completed' => false,
                        'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBO-dMU0u0EbLC2j2gBVu-BGgpP-P3dtw0KJnV04RprqHYNUjs-LaELu50fjHoIqvTIy-T1GCryAeJF0kQ4AbbOV33DPRWbDdYQX-0xzVFcF3UsRds_s30yz9Us_8wlTpMkqNbZdXU9Sh_DVzck0z1e5gaBh7krxd9_XWa39fZn1C9_iRRDvB6aw3dcpkXXfc-wAfg0pOs7DYFt-iq8kWrtjPI6rFujpnc1R6Rz5AV3kkPs2P-6T3Q8z2Y-KqklzDcwT3U9MQDcfhE',
                    ]
                ],
                'note' => null,
            ],
            [
                'id' => '#398-B',
                'customer' => 'Table 4 — Abebe',
                'elapsed_seconds' => 1485, // 24:45
                'instruction' => null,
                'status' => 'Preparing',
                'note' => 'Waiting for fresh Injera delivery.',
                'note_by' => 'Chef T.',
                'items' => [
                    [
                        'name' => 'Large Mixed Platter',
                        'quantity' => 1,
                        'completed' => false,
                        'image' => null,
                    ]
                ]
            ],
            [
                'id' => '#406-Z',
                'customer' => 'Table 2 — Sara',
                'elapsed_seconds' => 105, // 01:45
                'instruction' => null,
                'status' => 'New',
                'items' => [
                    [
                        'name' => 'Asa Gulash (Fish Stew)',
                        'quantity' => 2,
                        'completed' => false,
                        'image' => null,
                    ]
                ],
                'note' => null,
            ],
        ];

        return Inertia::render('KdsTablet', [
            'initialOrders' => $initialOrders,
        ]);
    }

    /**
     * Display the Chef Terminal (Menu Creator & Inventory).
     */
    public function chef()
    {
        // Sample catalog items
        $initialCatalog = [
            [
                'id' => 1,
                'name' => 'Doro Wat Supreme',
                'price' => 650,
                'category' => 'Mains (Wats & Tibs)',
                'description' => 'Slow-simmered chicken with linked Berbere sourcing.',
                'classification' => 'Yefisik', // Yetsome or Yefisik
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuA_3A0GycTz8LQntblLyl1mlbiGH9UtpFofmzh87m_0MtzWEIvRogwJ7j092Jzw5tORJ2D7ZWfOjfj8WZe6INngD_yci_k-mBTcuWlJUIugoOGtXGezKE7-5de9YUHsFX6HsUm-YYwfffK0XGKfZO7ldhS4xGCnn0_FmC_aHITsX6m8LrkDC0m9UupQahKf_M_xnZu9lMPSmYh_IemMbrex4rQTihDqisgKQ6pLnz2f5LrSOqxoJsxg6A-2GMVKBW6FRm8qBayeptc',
            ],
        ];

        // Sample inventory items
        $initialInventory = [
            [
                'id' => 'BERBERE_SPICE_KG',
                'name' => 'Berbere Spice',
                'stock' => 4.20,
                'unit' => 'kg',
                'threshold' => 2.00,
                'status' => 'Optimal',
            ],
            [
                'id' => 'TEFF_FLOUR_GR_BAG',
                'name' => 'Teff Flour (Grade A)',
                'stock' => 0.85,
                'unit' => 'bag',
                'threshold' => 1.50,
                'status' => 'Critical',
            ],
            [
                'id' => 'KIBBEH_BUTTER_L',
                'name' => 'Niter Kibbeh Butter',
                'stock' => 1.10,
                'unit' => 'L',
                'threshold' => 1.00,
                'status' => 'Low Stock',
            ],
            [
                'id' => 'MITMITA_POWDER_KG',
                'name' => 'Mitmita Powder',
                'stock' => 2.50,
                'unit' => 'kg',
                'threshold' => 1.00,
                'status' => 'Optimal',
            ],
        ];

        return Inertia::render('ChefTerminal', [
            'initialCatalog' => $initialCatalog,
            'initialInventory' => $initialInventory,
        ]);
    }
}
