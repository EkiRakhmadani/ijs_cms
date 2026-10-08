<?php

namespace Database\Seeders;

use App\Models\ContactChannel;
use App\Models\PageSeo;
use App\Models\SiteSetting;
use App\Models\UiString;
use Illuminate\Database\Seeder;

/**
 * The site copy that is not a service: contact channels, UI strings, and what
 * search engines see. Lifted verbatim from the frontend's own modules so the
 * CMS and the site cannot drift apart at the moment of the move.
 *
 * Page descriptions keep their `description_key` rather than a copy of the
 * sentence: the frontend lifts the opening sentence of the named UI string, so
 * editing that copy updates the description with it and the two cannot
 * disagree.
 *
 * Idempotent: re-running updates in place instead of duplicating.
 */
class SiteContentSeeder extends Seeder
{
    public function run(): void
    {
        SiteSetting::query()->updateOrCreate(['id' => 1], self::SITE);

        foreach (self::CHANNELS as $channel) {
            ContactChannel::updateOrCreate(['key' => $channel['key']], $channel);
        }

        foreach (self::UI_STRINGS as $string) {
            UiString::updateOrCreate(['key' => $string['key']], $string);
        }

        foreach (self::PAGES as $page) {
            PageSeo::updateOrCreate(['key' => $page['key']], $page);
        }
    }

    /** @var array<string, string> */
    private const SITE = [
        'url' => 'https://ptijs.com',
        'title' => 'IJS - Internusa Jayaabadi Sentosa',
        'name' => 'Internusa Jayaabadi Sentosa',
        'legal_name' => 'PT Internusa Jayaabadi Sentosa',
        'short_name' => 'IJS',
        'locale_id' => 'id_ID',
        'locale_en' => 'en_US',
    ];

    /** @var list<array<string, mixed>> */
    private const CHANNELS = [
        0 => [
            'key' => 'phone',
            'href' => 'tel:+622157973088',
            'label_id' => '+6221-5797-3088',
            'label_en' => null,
            'postal' => null,
            'position' => 0,
        ],
        1 => [
            'key' => 'email',
            'href' => 'mailto:services@ptijs.com',
            'label_id' => 'services@ptijs.com',
            'label_en' => null,
            'postal' => null,
            'position' => 1,
        ],
        2 => [
            'key' => 'instagram',
            'href' => 'https://instagram.com/lifeatijsjakarta',
            'label_id' => '@lifeatijsjakarta',
            'label_en' => null,
            'postal' => null,
            'position' => 2,
        ],
        3 => [
            'key' => 'linkedin',
            'href' => 'https://www.linkedin.com/company/pt-internusa-jayaabadi-sentosa',
            'label_id' => 'PT Internusa Jayaabadi Sentosa',
            'label_en' => null,
            'postal' => null,
            'position' => 3,
        ],
        4 => [
            'key' => 'address',
            'href' => 'https://maps.app.goo.gl/ykPkZjqMKhrxU8tJ7',
            'label_id' => 'Gedung Artha Graha Lt. 27, SCBD, Jl. Jenderal Sudirman, Senayan, Kec. Kebayoran Baru, Kota Jakarta Selatan, DKI Jakarta 12190',
            'label_en' => 'Artha Graha Building, 27th Floor, SCBD, Jl. Jenderal Sudirman, Senayan, Kebayoran Baru District, South Jakarta, DKI Jakarta 12190',
            'postal' => [
                'streetAddress' => 'Gedung Artha Graha Lt. 27, SCBD, Jl. Jenderal Sudirman, Senayan, Kebayoran Baru',
                'addressLocality' => 'Jakarta Selatan',
                'addressRegion' => 'DKI Jakarta',
                'postalCode' => '12190',
                'addressCountry' => 'ID',
            ],
            'position' => 4,
        ],
    ];

    /** @var list<array<string, mixed>> */
    private const UI_STRINGS = [
        0 => [
            'key' => 'lang.switch',
            'value_id' => 'Bahasa',
            'value_en' => 'Language',
            'position' => 0,
        ],
        1 => [
            'key' => 'lang.en',
            'value_id' => 'EN',
            'value_en' => 'EN',
            'position' => 1,
        ],
        2 => [
            'key' => 'lang.id',
            'value_id' => 'ID',
            'value_en' => 'ID',
            'position' => 2,
        ],
        3 => [
            'key' => 'nav.home',
            'value_id' => 'Beranda',
            'value_en' => 'Home',
            'position' => 3,
        ],
        4 => [
            'key' => 'nav.about',
            'value_id' => 'Tentang IJS',
            'value_en' => 'About IJS',
            'position' => 4,
        ],
        5 => [
            'key' => 'nav.services',
            'value_id' => 'Layanan',
            'value_en' => 'Services',
            'position' => 5,
        ],
        6 => [
            'key' => 'nav.products',
            'value_id' => 'Produk',
            'value_en' => 'Products',
            'position' => 6,
        ],
        7 => [
            'key' => 'nav.group.services',
            'value_id' => 'Layanan',
            'value_en' => 'Services',
            'position' => 7,
        ],
        8 => [
            'key' => 'nav.group.systems',
            'value_id' => 'Sistem',
            'value_en' => 'Systems',
            'position' => 8,
        ],
        9 => [
            'key' => 'nav.group.soon',
            'value_id' => 'Segera hadir',
            'value_en' => 'Coming soon',
            'position' => 9,
        ],
        10 => [
            'key' => 'nav.careers',
            'value_id' => 'Karier',
            'value_en' => 'Careers',
            'position' => 10,
        ],
        11 => [
            'key' => 'nav.cta',
            'value_id' => 'Hubungi Kami',
            'value_en' => 'Connect With Us',
            'position' => 11,
        ],
        12 => [
            'key' => 'nav.menuOpen',
            'value_id' => 'Buka menu',
            'value_en' => 'Open menu',
            'position' => 12,
        ],
        13 => [
            'key' => 'nav.menuClose',
            'value_id' => 'Tutup menu',
            'value_en' => 'Close menu',
            'position' => 13,
        ],
        14 => [
            'key' => 'nav.barHide',
            'value_id' => 'Sembunyikan bilah menu',
            'value_en' => 'Hide the menu bar',
            'position' => 14,
        ],
        15 => [
            'key' => 'nav.barShow',
            'value_id' => 'Tampilkan bilah menu',
            'value_en' => 'Show the menu bar',
            'position' => 15,
        ],
        16 => [
            'key' => 'frame.close',
            'value_id' => 'Tutup',
            'value_en' => 'Close',
            'position' => 16,
        ],
        17 => [
            'key' => 'frame.openOn',
            'value_id' => 'Buka di',
            'value_en' => 'Open on',
            'position' => 17,
        ],
        18 => [
            'key' => 'frame.careers',
            'value_id' => 'Karier di IJS',
            'value_en' => 'Careers at IJS',
            'position' => 18,
        ],
        19 => [
            'key' => 'connect.lead',
            'value_id' => 'IJS di LinkedIn — kabar perusahaan, orang-orangnya, dan lowongan.',
            'value_en' => 'IJS on LinkedIn — company updates, people and openings.',
            'position' => 19,
        ],
        20 => [
            'key' => 'connect.linkedinCta',
            'value_id' => 'Buka LinkedIn IJS',
            'value_en' => 'Open IJS on LinkedIn',
            'position' => 20,
        ],
        21 => [
            'key' => 'connect.otherLead',
            'value_id' => 'Atau hubungi IJS langsung:',
            'value_en' => 'Or reach IJS directly:',
            'position' => 21,
        ],
        22 => [
            'key' => 'connect.phone',
            'value_id' => 'Telepon',
            'value_en' => 'Phone',
            'position' => 22,
        ],
        23 => [
            'key' => 'connect.email',
            'value_id' => 'Email',
            'value_en' => 'Email',
            'position' => 23,
        ],
        24 => [
            'key' => 'connect.instagram',
            'value_id' => 'Instagram',
            'value_en' => 'Instagram',
            'position' => 24,
        ],
        25 => [
            'key' => 'connect.address',
            'value_id' => 'Kantor',
            'value_en' => 'Office',
            'position' => 25,
        ],
        26 => [
            'key' => 'about.label',
            'value_id' => 'SIAPA KAMI',
            'value_en' => 'WHAT WE ARE',
            'position' => 26,
        ],
        27 => [
            'key' => 'about.p1',
            'value_id' => '[[PT Internusa Jayaabadi Sentosa]] (IJS) merupakan [[strategic partner]] yang menyediakan [[end-to-end services]] untuk mendukung kebutuhan bisnis klien maupun business unit. IJS telah mendukung berbagai industri, mulai dari [[hospitality, F&B, Oil & Gas, mall, property development, hingga steel manufacturing]], dengan cakupan operasional di beberapa wilayah Indonesia serta Timor Leste.',
            'value_en' => '[[PT Internusa Jayaabadi Sentosa]] (IJS) is a [[strategic partner]] providing [[end-to-end services]] to support the business needs of clients and business units alike. IJS has supported a range of industries — [[hospitality, F&B, oil & gas, malls, property development, and steel manufacturing]] — with operations spanning several regions of Indonesia as well as Timor Leste.',
            'position' => 27,
        ],
        28 => [
            'key' => 'about.p2',
            'value_id' => 'IJS memiliki tujuan untuk menjadi [[integrated solution]] yang mendukung dan menghubungkan berbagai kebutuhan bisnis secara kolektif. Dalam menjalankan perannya, IJS menekankan transparansi proses, kepuasan stakeholder, serta peran sebagai strategic partner dalam membantu stakeholder mencapai aspirasinya.',
            'value_en' => 'IJS exists to be the [[integrated solution]] that supports and connects varied business needs collectively. In that role, IJS puts the weight on transparency of process, stakeholder satisfaction, and acting as the strategic partner that helps stakeholders reach what they are aiming for.',
            'position' => 28,
        ],
        29 => [
            'key' => 'purpose.label',
            'value_id' => 'MENGAPA KAMI ADA',
            'value_en' => 'WHY WE EXIST',
            'position' => 29,
        ],
        30 => [
            'key' => 'purpose.quote',
            'value_id' => '“To Become An Integrated Solution That Embraces Collective Business”',
            'value_en' => '“To Become An Integrated Solution That Embraces Collective Business”',
            'position' => 30,
        ],
        31 => [
            'key' => 'purpose.link',
            'value_id' => 'Selengkapnya Tentang IJS',
            'value_en' => 'Learn More About IJS',
            'position' => 31,
        ],
        32 => [
            'key' => 'purpose.p1',
            'value_id' => 'IJS hadir untuk menjadi mitra solusi bisnis terintegrasi yang menghubungkan berbagai keahlian, fungsi, dan layanan dalam satu ekosistem yang saling mendukung.
Melalui pendekatan yang terintegrasi, IJS membantu menciptakan proses kerja yang lebih transparan, kolaboratif, dan efektif dalam menjawab kebutuhan bisnis serta mendukung pencapaian aspirasi setiap stakeholder.',
            'value_en' => 'IJS exists to be the integrated business-solution partner that connects varied expertise, functions, and services into a single mutually supporting ecosystem.
Through that integrated approach, IJS helps create ways of working that are more transparent, more collaborative, and more effective at answering business needs and supporting what each stakeholder is working toward.',
            'position' => 32,
        ],
        33 => [
            'key' => 'purpose.p2',
            'value_id' => 'IJS meyakini bahwa pertumbuhan bisnis yang berkelanjutan dibangun melalui kolaborasi. Karena itu, setiap solusi yang diberikan tidak hanya berfokus pada kebutuhan saat ini, tetapi juga menciptakan nilai jangka panjang bagi bisnis dan seluruh stakeholder.',
            'value_en' => 'IJS holds that sustainable business growth is built through collaboration. Every solution we give is therefore aimed not only at the need of the moment, but at creating long-term value for the business and for every stakeholder in it.',
            'position' => 33,
        ],
        34 => [
            'key' => 'values.label',
            'value_id' => 'NILAI INTI KAMI',
            'value_en' => 'OUR CORE VALUES',
            'position' => 34,
        ],
        35 => [
            'key' => 'values.subLead',
            'value_id' => 'IJS sebagai Business',
            'value_en' => 'IJS as Business',
            'position' => 35,
        ],
        36 => [
            'key' => 'values.subTail',
            'value_id' => 'Menghadirkan Layanan End-to-End.',
            'value_en' => 'Providing End-to-End Services.',
            'position' => 36,
        ],
        37 => [
            'key' => 'values.rayHint',
            'value_id' => 'Arahkan kursor ke sebuah huruf untuk mengikuti cahayanya',
            'value_en' => 'Hover a letter to follow its light',
            'position' => 37,
        ],
        38 => [
            'key' => 'values.rayIdle',
            'value_id' => 'Delapan huruf, satu titik fokus',
            'value_en' => 'Eight letters, one focus',
            'position' => 38,
        ],
        39 => [
            'key' => 'services.label',
            'value_id' => 'LAYANAN KAMI',
            'value_en' => 'OUR SERVICES',
            'position' => 39,
        ],
        40 => [
            'key' => 'services.orbitHint',
            'value_id' => 'Putar orbitnya atau gunakan tombol panah',
            'value_en' => 'Drag the orbit or use the arrows',
            'position' => 40,
        ],
        41 => [
            'key' => 'services.cta',
            'value_id' => 'Layanan Kami',
            'value_en' => 'Our Services',
            'position' => 41,
        ],
        42 => [
            'key' => 'services.ghostTop',
            'value_id' => 'LAYANAN',
            'value_en' => 'OUR',
            'position' => 42,
        ],
        43 => [
            'key' => 'services.ghostBottom',
            'value_id' => 'KAMI',
            'value_en' => 'SERVICES',
            'position' => 43,
        ],
        44 => [
            'key' => 'services.pickerTitle',
            'value_id' => 'Layanan',
            'value_en' => 'Services',
            'position' => 44,
        ],
        45 => [
            'key' => 'services.itemsNav',
            'value_id' => 'Rincian layanan',
            'value_en' => 'Service details',
            'position' => 45,
        ],
        46 => [
            'key' => 'services.goToStop',
            'value_id' => 'Ke rincian',
            'value_en' => 'Go to detail',
            'position' => 46,
        ],
        47 => [
            'key' => 'services.prev',
            'value_id' => 'Layanan sebelumnya',
            'value_en' => 'Previous service',
            'position' => 47,
        ],
        48 => [
            'key' => 'services.next',
            'value_id' => 'Layanan berikutnya',
            'value_en' => 'Next service',
            'position' => 48,
        ],
        49 => [
            'key' => 'social.label',
            'value_id' => 'MEDIA SOSIAL',
            'value_en' => 'SOCIAL MEDIA',
            'position' => 49,
        ],
        50 => [
            'key' => 'social.embedTitle',
            'value_id' => 'Postingan Instagram',
            'value_en' => 'Instagram post',
            'position' => 50,
        ],
        51 => [
            'key' => 'social.viewPost',
            'value_id' => 'Lihat postingan ini di Instagram',
            'value_en' => 'View this post on Instagram',
            'position' => 51,
        ],
        52 => [
            'key' => 'social.openInstagram',
            'value_id' => 'Buka di Instagram',
            'value_en' => 'Open on Instagram',
            'position' => 52,
        ],
        53 => [
            'key' => 'social.openProfile',
            'value_id' => 'Lihat Instagram IJS',
            'value_en' => 'See the IJS Instagram',
            'position' => 53,
        ],
        54 => [
            'key' => 'social.seeAll',
            'value_id' => 'Lihat semua postingan —',
            'value_en' => 'See all posts —',
            'position' => 54,
        ],
        55 => [
            'key' => 'footer.tagline',
            'value_id' => 'Pahami Perusahaan Anda,
Orang-Orang Anda, dan Pasar Anda',
            'value_en' => 'Understand Your Company,
Your People, and Your Market',
            'position' => 55,
        ],
        56 => [
            'key' => 'footer.emailPlaceholder',
            'value_id' => 'Email Anda',
            'value_en' => 'Your Email',
            'position' => 56,
        ],
        57 => [
            'key' => 'footer.subscribe',
            'value_id' => 'Tetap Terhubung',
            'value_en' => 'Stay Connected',
            'position' => 57,
        ],
        58 => [
            'key' => 'footer.copyright',
            'value_id' => '© 2026 PTIJS',
            'value_en' => '© 2026 PTIJS',
            'position' => 58,
        ],
        59 => [
            'key' => 'footer.backToTop',
            'value_id' => 'Kembali ke Atas',
            'value_en' => 'Back to Top',
            'position' => 59,
        ],
        60 => [
            'key' => 'notFound.title',
            'value_id' => 'Halaman tidak ditemukan',
            'value_en' => 'Page not found',
            'position' => 60,
        ],
        61 => [
            'key' => 'notFound.home',
            'value_id' => 'Kembali ke Beranda →',
            'value_en' => 'Back to Home →',
            'position' => 61,
        ],
        62 => [
            'key' => 'error.label',
            'value_id' => 'Kesalahan',
            'value_en' => 'Error',
            'position' => 62,
        ],
        63 => [
            'key' => 'error.title',
            'value_id' => 'Terjadi kesalahan',
            'value_en' => 'Something went wrong',
            'position' => 63,
        ],
        64 => [
            'key' => 'error.body',
            'value_id' => 'Halaman ini tidak dapat ditampilkan. Coba lagi, atau kembali ke Beranda.',
            'value_en' => 'This page could not be shown. Try again, or head back to the home page.',
            'position' => 64,
        ],
        65 => [
            'key' => 'error.retry',
            'value_id' => 'Coba lagi',
            'value_en' => 'Try again',
            'position' => 65,
        ],
        66 => [
            'key' => 'error.home',
            'value_id' => 'Kembali ke Beranda →',
            'value_en' => 'Back to Home →',
            'position' => 66,
        ],
    ];

    /** @var list<array<string, mixed>> */
    private const PAGES = [
        0 => [
            'key' => 'home',
            'path' => '/',
            'title_id' => null,
            'title_en' => null,
            'description_key' => 'about.p1',
            'description_id' => null,
            'description_en' => null,
            'position' => 0,
        ],
        1 => [
            'key' => 'about',
            'path' => '/about',
            'title_id' => 'Tentang Kami',
            'title_en' => 'About Us',
            'description_key' => 'about.p2',
            'description_id' => null,
            'description_en' => null,
            'position' => 1,
        ],
    ];
}
