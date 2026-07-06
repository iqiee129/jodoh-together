<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            [
                'name' => 'Seri Anggun Bridal',
                'category' => 'attire',
                'state' => 'Penang',
                'location' => 'George Town',
                'price' => 1200.00,
                'phone' => '0123456789',
                'email' => 'serianggun@gmail.com',
                'description' => 'Bridal dress rental, groom attire, and simple makeup package.',
                'image_url' => null,
                'is_active' => true,
            ],
            [
                'name' => 'PG Moments Photography',
                'category' => 'photography',
                'state' => 'Penang',
                'location' => 'Bayan Lepas',
                'price' => 1500.00,
                'phone' => '0123456789',
                'email' => 'pgmoments@gmail.com',
                'description' => 'Wedding photography for nikah, sanding, and outdoor photoshoot.',
                'image_url' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Dapur Cinta Catering',
                'category' => 'catering',
                'state' => 'Kedah',
                'location' => 'Alor Setar',
                'price' => 3800.00,
                'phone' => '0134567890',
                'email' => 'dapurcinta@gmail.com',
                'description' => 'Catering package for 300 pax including rice, dishes, drinks, and dessert.',
                'image_url' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Mawar Indah Decoration',
                'category' => 'decoration',
                'state' => 'Perak',
                'location' => 'Ipoh',
                'price' => 2500.00,
                'phone' => '0145678901',
                'email' => 'mawarindah@gmail.com',
                'description' => 'Pelamin, walkway decoration, flower arrangement, and table setup.',
                'image_url' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Cinta Event Hall',
                'category' => 'venue',
                'state' => 'Perlis',
                'location' => 'Kangar',
                'price' => 4000.00,
                'phone' => '0156789012',
                'email' => 'cintaeventhall@gmail.com',
                'description' => 'Wedding hall rental with basic lighting, tables, chairs, and parking space.',
                'image_url' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Sweet Memory Makeup',
                'category' => 'makeup',
                'state' => 'Kedah',
                'location' => 'Sungai Petani',
                'price' => 850.00,
                'phone' => '0167890123',
                'email' => 'sweetmemory@gmail.com',
                'description' => 'Bridal makeup for nikah and sanding, including touch-up service.',
                'image_url' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Kad Kahwin Studio',
                'category' => 'invitation',
                'state' => 'Penang',
                'location' => 'Butterworth',
                'price' => 300.00,
                'phone' => '0178901234',
                'email' => 'kadkahwinstudio@gmail.com',
                'description' => 'Custom wedding invitation card design and printing service.',
                'image_url' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Harmoni Sound System',
                'category' => 'entertainment',
                'state' => 'Perak',
                'location' => 'Taiping',
                'price' => 1000.00,
                'phone' => '0189012345',
                'email' => 'harmonisound@gmail.com',
                'description' => 'PA system, microphone, speaker, and basic wedding event sound setup.',
                'image_url' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Bunga Kasih Florist',
                'category' => 'decoration',
                'state' => 'Perlis',
                'location' => 'Arau',
                'price' => 750.00,
                'phone' => '0190123456',
                'email' => 'bungakasih@gmail.com',
                'description' => 'Fresh flower bouquet, car decoration, and mini pelamin flower setup.',
                'image_url' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Lensa Kasih Videography',
                'category' => 'videography',
                'state' => 'Kedah',
                'location' => 'Kulim',
                'price' => 2200.00,
                'phone' => '01123456789',
                'email' => 'lensakasih@gmail.com',
                'description' => 'Wedding video highlight, full event recording, and edited montage.',
                'image_url' => null,
                'is_active' => true,
            ],
        ];

        foreach ($vendors as $vendor) {
            Vendor::updateOrCreate(
                ['name' => $vendor['name']],
                $vendor
            );
        }
    }
}