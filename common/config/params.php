<?php
return [
    'pmcEmail' => 'pmc1gap@gmail.com',
    'adminEmail' => 'it@gajahtex.com',
    'supportEmail' => 'software@gajahtex.com',
    'senderEmail' => 'software@gajahtex.com',
    'senderName' => 'software@gajahtex.com',
    'user.passwordResetTokenExpire' => 3600,
    'rbac_roles' => [
        'developer' => 'Developer',
        'registered' => 'Registered',
        'super_admin' => 'Super Admin',
        'dirut' => 'Direktur Utama',
        'dir_marketing' => 'Direktur Marketing',
        'manager' => 'Manager',
        'mgr_marketing' => 'Manager Marketing',
        'marketing' => 'Marketing',
        'kabag_pmc' => 'Kabag PMC',
        'pmc' => 'PMC',
        'inspecting' => 'Inspecting',
        'gudang_jadi' => 'Gudang Jadi',
        'processing' => 'Bagian Proses',
        'admin_persiapan' => 'Admin Persiapan',
        'staff_gudang_greige' => 'Staff Gudang Greige',
    ],
    'meterToYard' => 1.0936133,
    'yardToMeter' => 0.9144,
    'approval_status' => [
        'belum_diajukan' => 'Belum Diajukan',
        'menunggu' => 'Menunggu Persetujuan',
        'disetujui' => 'Disetujui',
        'ditolak' => 'Ditolak'
    ],
    'form_token_param' => 'gap2_form_token_session',
    'hints'=>[
        'trn-retur-buyer/view'=>[
            'Retur buyer hanya bisa diposting oleh QC, karena akan ditentukan keputusan retur nya.',
            'Setelah diposting, semua qty pada dokumen ini akan masuk ke stock bahan baku dengan jenis gudang ex finish.',
            'Unit akan dikonversi menyesuaikan dengan unit pada greige group, khusus panjang. jika pcs atau kg, tidak dapat dikonversi.'
        ]
    ],
    'company'=>[
        'nama'=>'PT. GAJAH ANGKASA PERKASA',
        'alamat'=>'JL. JENDRAL SUDIRMAN NO.823 BANDUNG',
        'phone'=>''
    ],
    'kode_dokumen'=>[
        'sc' => 'GAP-FRM-MKT-04',
        'wo' => 'GAP-FRM-PMC-04',
        'mo' => 'GAP-FRM-MKT-06',
        'pfp' => 'GAP-FRM-PMC-05',
    ],

    'whacenterDeviceId' => '96f3f20e5d0fdea57c012453fef4c686',
];