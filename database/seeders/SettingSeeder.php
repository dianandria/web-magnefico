<?php 
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Bagian Hero
            [
                'key' => 'hero_image',
                'value' => 'hero-image.jpg',
                'type' => 'image'
            ],
            [
                'key' => 'hero_title',
                'value' => 'Designing and Building Intelligent Digital Experiences',
                'type' => 'text'
            ],
            [
                'key' => 'hero_subtitle',
                'value' => 'We combine experience, creativity, and technology to help brands achieve real results.',
                'type' => 'textarea'
            ],
            [
                'key' => 'home_video',
                'value' => '',
                'type' => 'video'
            ],
            [
                'key' => 'home_video_title',
                'value' => 'Make and be whatever you want',
                'type' => 'text'
            ],
            // Bagian Kontak & Ajakan (CTA)
            [
                'key' => 'cta_title',
                'value' => 'Ready to work with us ?',
                'type' => 'text'
            ],
            [
                'key' => 'cta_subtitle',
                'value' => 'Level up your business with us!',
                'type' => 'text'
            ],
            
            // Footer
            [
                'key' => 'contact_email',
                'value' => 'hello@magnefico.com', // Sesuaikan dengan email asli
                'type' => 'text'
            ],
            [
                'key' => 'footer_copyright',
                'value' => '©Copyright Magnefico Creative 2026. All rights reserved.',
                'type' => 'text'
            ],
        ];

        // Gunakan upsert atau updateOrCreate agar jika seeder dijalankan ulang, 
        // tidak terjadi duplikasi data.
        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']], // Cari berdasarkan key
                [
                    'value' => $setting['value'],
                    'type' => $setting['type']
                ]
            );
        }
    }
}