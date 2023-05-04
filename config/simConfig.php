<?php

return [
    'adminToken' => '2bb9066182b30762ac9743be3ec5e8c1f2de16cf5b0f1dd50e6a4a85930f9ded',
    // admintokenvippro2023@!!!!!@@@@

    'status' => [
        'balance' => ['Refunded', 'Success', 'On-hold'],
        'activity' => ['Aborted', 'Success', 'Working'],
        'sims' => ['Maintained', 'Available', 'Busy'],
        'network' => ['Maintained', 'Available', 'Busy'],
        'service' => ['Maintained', 'Available', 'Busy']
    ],

    'emergencyHeader' => [
        'active' => false,
        'context' => ''
    ],

    'navBar' => [
        'border1' => [
            'separator' => true,
            'name' => 'Rents'
        ],
        'dashboard' => [
            'active' => true,
            'icon' => '<svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>',
            'name' => 'Dashboard',
            'redirect' => 'dashboard',
            'singleLink' => false,
            'admin' => false,
            'dropdown' => false,
            'dropdownContent' => [
                [
                    'link' => '',
                    'name' => ''
                ],
            ]
        ],

        'basicRent' => [
            'active' => true,
            'icon' => '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"></path></svg>',
            'name' => 'Rent An Number',
            'redirect' => 'basicRent',
            'singleLink' => false,
            'dropdown' => false,
            'dropdownContent' => [
                [
                    'link' => '',
                    'name' => ''
                ],
            ]
        ],

        'customRent' => [
            'active' => true,
            'icon' => '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"></path></svg>',
            'name' => 'Custom Rent',
            'redirect' => 'customRent',
            'singleLink' => false,
            'dropdown' => false,
            'dropdownContent' => [
                [
                    'link' => '',
                    'name' => ''
                ],
            ]
        ],

        'rentHistory' => [
            'active' => true,
            'icon' => '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"></path></svg>',
            'name' => 'Rent History',
            'redirect' => 'rentHistory',
            'singleLink' => false,
            'dropdown' => false,
            'dropdownContent' => [
                [
                    'link' => '',
                    'name' => ''
                    ],
                ]
        ],
    ],

    'adminNavbar' => [
        'users' => [
            'active' => true,
            'icon' => '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path></svg>',
            'name' => 'Người Dùng',
            'redirect' => 'admin.users',
            'singleLink' => false,
            'dropdown' => false,
            'dropdownContent' => [
                [
                    'link' => '',
                    'name' => ''
                ],
            ]
        ],
        'sims' => [
            'active' => true,
            'icon' => '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 01-1.125-1.125v-3.75zM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 01-1.125-1.125v-8.25zM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 01-1.125-1.125v-2.25z"></path>
</svg>',
            'name' => 'Số Điện Thoại',
            'redirect' => 'admin.sims',
            'singleLink' => false,
            'dropdown' => false,
            'dropdownContent' => [
                [
                    'link' => '',
                    'name' => ''
                ],
            ]
        ],
        'services' => [
            'active' => true,
            'icon' => '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418"></path>
</svg>',
            'name' => 'Dịch Vụ',
            'redirect' => 'admin.services',
            'singleLink' => false,
            'dropdown' => false,
            'dropdownContent' => [
                [
                    'link' => '',
                    'name' => ''
                ],
            ]
        ],
        'networks' => [
            'active' => true,
            'icon' => '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12.75 19.5v-.75a7.5 7.5 0 00-7.5-7.5H4.5m0-6.75h.75c7.87 0 14.25 6.38 14.25 14.25v.75M6 18.75a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"></path>
</svg>',
            'name' => 'Mạng',
            'redirect' => 'admin.networks',
            'singleLink' => false,
            'dropdown' => false,
            'dropdownContent' => [
                [
                    'link' => '',
                    'name' => ''
                ],
            ]
        ],
    ]
];