<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * The live service copy, lifted verbatim from the frontend's
 * src/data/servicesData.js so the CMS and the site cannot drift apart.
 *
 * Idempotent: re-running updates in place instead of duplicating.
 */
class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        foreach (self::SERVICES as $position => $data) {
            $service = Service::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'title' => $data['title'],
                    'icon' => $data['icon'],
                    'description_id' => $data['description']['id'],
                    'description_en' => $data['description']['en'],
                    'position' => $position,
                ],
            );

            $service->items()->delete();

            foreach ($data['items'] as $index => $item) {
                $service->items()->create([
                    'title' => $item['title'],
                    'desc_id' => $item['desc']['id'],
                    'desc_en' => $item['desc']['en'],
                    'position' => $index,
                ]);
            }
        }
    }

    /**
     * @var list<array{slug:string,title:string,icon:?string,description:array{id:string,en:string},items:list<array{title:string,desc:array{id:string,en:string}}>}>
     */
    private const SERVICES = [
        0 => [
            'slug' => 'services-finance-accounting-tax',
            'title' => 'Finance, Accounting & Tax',
            'icon' => '$',
            'description' => [
                'id' => 'IJS mendukung unit-unit bisnis dalam grup melalui pengelolaan keuangan dan perpajakan yang tertib, akurat, dan selaras dengan kebutuhan pengambilan keputusan. Cakupan kami meliputi penyusunan laporan, perencanaan anggaran, hingga kepatuhan pajak, sehingga setiap unit bisnis memiliki dasar keuangan yang jelas untuk beroperasi dan bertumbuh.',
                'en' => 'IJS supports the business units across the group with financial and tax management that is orderly, accurate, and aligned with the needs of decision-making. Our scope runs from financial reporting and budget planning through to tax compliance, so that every business unit operates and grows on a clear financial footing.',
            ],
            'items' => [
                0 => [
                    'title' => 'Financial Reporting & Analysis',
                    'desc' => [
                        'id' => 'Penyusunan laporan keuangan yang akurat serta analisis kondisi finansial untuk mendukung pengambilan keputusan yang tepat.',
                        'en' => 'Accurate financial reporting and analysis of financial position, to support well-founded decisions.',
                    ],
                ],
                1 => [
                    'title' => 'Financial Planning & Analysis (FP&A)',
                    'desc' => [
                        'id' => 'Perencanaan dan analisis keuangan yang membantu unit bisnis merumuskan strategi dan proyeksi ke depan.',
                        'en' => 'Financial planning and analysis that helps business units shape forward strategy and projections.',
                    ],
                ],
                2 => [
                    'title' => 'Budget Planning & Monitoring',
                    'desc' => [
                        'id' => 'Penyusunan serta pemantauan anggaran secara berkala untuk menjaga disiplin dan efisiensi pengeluaran.',
                        'en' => 'Preparing budgets and monitoring them on a regular cycle, keeping spending disciplined and efficient.',
                    ],
                ],
                3 => [
                    'title' => 'Tax Compliance & Administration',
                    'desc' => [
                        'id' => 'Pengelolaan kewajiban dan administrasi perpajakan perusahaan sesuai ketentuan yang berlaku.',
                        'en' => 'Managing the company’s tax obligations and administration in line with prevailing regulations.',
                    ],
                ],
                4 => [
                    'title' => 'Tax Advisory & Support',
                    'desc' => [
                        'id' => 'Pemberian masukan dan rekomendasi atas permasalahan perpajakan yang dihadapi unit bisnis.',
                        'en' => 'Guidance and recommendations on the tax matters business units run into.',
                    ],
                ],
                5 => [
                    'title' => 'Financial & Tax Review',
                    'desc' => [
                        'id' => 'Peninjauan aspek keuangan dan perpajakan dalam aktivitas bisnis guna memastikan kepatuhan dan akurasi.',
                        'en' => 'Reviewing the financial and tax aspects of business activity to confirm compliance and accuracy.',
                    ],
                ],
            ],
        ],
        1 => [
            'slug' => 'services-legal',
            'title' => 'Legal',
            'icon' => '§',
            'description' => [
                'id' => 'Tim Legal IJS memberikan dukungan hukum menyeluruh bagi unit-unit bisnis dalam grup, mulai dari perizinan usaha hingga dokumentasi dan kerja sama dengan pihak ketiga. Pendekatan kami memastikan setiap aktivitas bisnis berjalan sesuai koridor hukum yang berlaku.',
                'en' => 'The IJS legal team provides end-to-end legal support to the business units across the group, from business licensing through to documentation and third-party cooperation. Our approach keeps every business activity within the bounds of applicable law.',
            ],
            'items' => [
                0 => [
                    'title' => 'Corporate Legal Support',
                    'desc' => [
                        'id' => 'Dukungan hukum untuk menunjang kegiatan operasional dan kebutuhan legal perusahaan sehari-hari.',
                        'en' => 'Legal support for day-to-day operations and the company’s everyday legal needs.',
                    ],
                ],
                1 => [
                    'title' => 'Business Licensing & Permits',
                    'desc' => [
                        'id' => 'Pengurusan legalitas dan perizinan usaha yang dibutuhkan dalam aktivitas bisnis.',
                        'en' => 'Handling the corporate legality and business permits that business activity requires.',
                    ],
                ],
                2 => [
                    'title' => 'Legal Documentation',
                    'desc' => [
                        'id' => 'Penyusunan dan pengelolaan dokumen hukum sesuai kebutuhan aktivitas perusahaan.',
                        'en' => 'Drafting and maintaining legal documents as the company’s activities call for them.',
                    ],
                ],
                3 => [
                    'title' => 'Legal Review for Business Activities',
                    'desc' => [
                        'id' => 'Peninjauan aspek hukum atas aktivitas bisnis untuk mengidentifikasi risiko dan memastikan kepatuhan.',
                        'en' => 'Reviewing the legal side of business activity to surface risk and confirm compliance.',
                    ],
                ],
                4 => [
                    'title' => 'Tax Advisory & Support',
                    'desc' => [
                        'id' => 'Pemberian masukan dan rekomendasi atas permasalahan perpajakan yang dihadapi unit bisnis.',
                        'en' => 'Guidance and recommendations on the tax matters business units run into.',
                    ],
                ],
                5 => [
                    'title' => 'Third-Party Cooperation & Agreement Support',
                    'desc' => [
                        'id' => 'Pendampingan hukum dalam kerja sama dan perjanjian dengan pihak ketiga.',
                        'en' => 'Legal accompaniment through cooperation and agreements with third parties.',
                    ],
                ],
            ],
        ],
        2 => [
            'slug' => 'services-hrga',
            'title' => 'HRGA',
            'icon' => '⚕',
            'description' => [
                'id' => 'IJS mendukung pengelolaan sumber daya manusia dan urusan umum bagi unit-unit bisnis dalam grup, mencakup rekrutmen, pengembangan karyawan, hingga administrasi operasional — membangun fondasi organisasi yang kuat dan tertata bagi setiap unit.',
                'en' => 'IJS supports human resources and general affairs across the group’s business units — covering recruitment, employee development, and operational administration — building each unit a strong, well-ordered organisational foundation.',
            ],
            'items' => [
                0 => [
                    'title' => 'Recruitment & Talent Acquisition',
                    'desc' => [
                        'id' => 'Proses pencarian dan seleksi kandidat sesuai kebutuhan dan standar perusahaan.',
                        'en' => 'Sourcing and selecting candidates against the company’s needs and standards.',
                    ],
                ],
                1 => [
                    'title' => 'Employee Development',
                    'desc' => [
                        'id' => 'Program pengembangan kompetensi dan potensi karyawan secara berkelanjutan.',
                        'en' => 'Ongoing programmes that build employees’ competencies and potential.',
                    ],
                ],
                2 => [
                    'title' => 'Payroll Management',
                    'desc' => [
                        'id' => 'Pengelolaan proses payroll dan administrasi penggajian karyawan.',
                        'en' => 'Running the payroll process and the administration of employee remuneration.',
                    ],
                ],
                3 => [
                    'title' => 'Industrial Relations',
                    'desc' => [
                        'id' => 'Pengelolaan hubungan kerja antara perusahaan dan karyawan sesuai ketentuan ketenagakerjaan.',
                        'en' => 'Managing the working relationship between company and employees under labour regulations.',
                    ],
                ],
                4 => [
                    'title' => 'General Affairs & Administrative Support',
                    'desc' => [
                        'id' => 'Dukungan operasional, fasilitas, dan administrasi umum bagi kelancaran kerja perusahaan.',
                        'en' => 'Operational, facilities, and general administrative support that keeps the company running smoothly.',
                    ],
                ],
            ],
        ],
        3 => [
            'slug' => 'services-it',
            'title' => 'Information & Technology',
            'icon' => '⌘',
            'description' => [
                'id' => 'Divisi IT IJS memastikan sistem dan infrastruktur teknologi unit-unit bisnis berjalan optimal, mulai dari dukungan perangkat harian hingga solusi teknologi yang menunjang operasional bisnis secara menyeluruh.',
                'en' => 'The IJS IT division keeps the business units’ systems and technology infrastructure running at their best — from day-to-day device support through to technology solutions that carry business operations as a whole.',
            ],
            'items' => [
                0 => [
                    'title' => 'IT Support',
                    'desc' => [
                        'id' => 'Dukungan teknis untuk menunjang kelancaran aktivitas kerja sehari-hari.',
                        'en' => 'Technical support that keeps day-to-day work moving.',
                    ],
                ],
                1 => [
                    'title' => 'Work System Support',
                    'desc' => [
                        'id' => 'Pendampingan atas sistem kerja dan teknologi yang digunakan dalam operasional perusahaan.',
                        'en' => 'Support for the work systems and technology the company operates on.',
                    ],
                ],
                2 => [
                    'title' => 'Hardware & Device Support',
                    'desc' => [
                        'id' => 'Dukungan pemeliharaan perangkat dan device yang digunakan dalam bekerja.',
                        'en' => 'Maintenance support for the hardware and devices people work on.',
                    ],
                ],
                3 => [
                    'title' => 'IT Infrastructure Support',
                    'desc' => [
                        'id' => 'Pengelolaan kebutuhan infrastruktur teknologi untuk menunjang sistem kerja perusahaan.',
                        'en' => 'Managing the technology infrastructure the company’s work systems depend on.',
                    ],
                ],
                4 => [
                    'title' => 'Technology Solutions for Business Operations',
                    'desc' => [
                        'id' => 'Perumusan solusi teknologi yang mendukung kelancaran operasional bisnis.',
                        'en' => 'Shaping technology solutions that keep business operations running smoothly.',
                    ],
                ],
            ],
        ],
        4 => [
            'slug' => 'services-internal-audit',
            'title' => 'Internal Audit',
            'icon' => '✓',
            'description' => [
                'id' => 'Fungsi Internal Audit IJS memberikan penilaian independen atas efektivitas proses dan pengendalian internal di setiap unit bisnis, sehingga risiko dapat diidentifikasi lebih awal dan perbaikan dapat dilakukan secara terarah.',
                'en' => 'The IJS internal audit function gives an independent assessment of how effectively processes and internal controls work in each business unit, so that risk surfaces early and improvement can be aimed where it counts.',
            ],
            'items' => [
                0 => [
                    'title' => 'Internal Audit Services',
                    'desc' => [
                        'id' => 'Pemeriksaan internal untuk menilai efektivitas proses dan aktivitas perusahaan.',
                        'en' => 'Internal examination that assesses how effective the company’s processes and activities are.',
                    ],
                ],
                1 => [
                    'title' => 'Business Activity Review',
                    'desc' => [
                        'id' => 'Peninjauan aktivitas bisnis untuk memastikan pelaksanaan sesuai ketentuan yang berlaku.',
                        'en' => 'Reviewing business activity to confirm it is carried out under the applicable rules.',
                    ],
                ],
                2 => [
                    'title' => 'Internal Control Review',
                    'desc' => [
                        'id' => 'Evaluasi atas penerapan kontrol internal dalam proses dan aktivitas bisnis.',
                        'en' => 'Evaluating how internal controls are applied across business processes and activities.',
                    ],
                ],
                3 => [
                    'title' => 'Compliance & Process Evaluation',
                    'desc' => [
                        'id' => 'Penilaian atas kesesuaian proses dengan ketentuan dan kebijakan perusahaan.',
                        'en' => 'Assessing whether processes line up with company regulations and policy.',
                    ],
                ],
                4 => [
                    'title' => 'Audit Findings & Improvement Recommendations',
                    'desc' => [
                        'id' => 'Penyampaian temuan audit beserta rekomendasi sebagai dasar perbaikan.',
                        'en' => 'Reporting audit findings together with recommendations to act on.',
                    ],
                ],
            ],
        ],
        5 => [
            'slug' => 'services-business-process',
            'title' => 'Business Process',
            'icon' => '⚙',
            'description' => [
                'id' => 'IJS membantu unit-unit bisnis membangun proses kerja yang terstruktur dan efisien, mulai dari penyusunan SOP hingga pemantauan implementasinya, sehingga operasional berjalan konsisten dan dapat diukur.',
                'en' => 'IJS helps business units build work processes that are structured and efficient — from writing SOPs through to monitoring how they are implemented — so operations run consistently and can be measured.',
            ],
            'items' => [
                0 => [
                    'title' => 'SOP Development',
                    'desc' => [
                        'id' => 'Penyusunan Standard Operating Procedure (SOP) untuk mendukung proses kerja yang terstruktur.',
                        'en' => 'Writing Standard Operating Procedures (SOPs) that give work processes their structure.',
                    ],
                ],
                1 => [
                    'title' => 'Business Process Mapping',
                    'desc' => [
                        'id' => 'Pemetaan alur proses bisnis untuk memberikan gambaran menyeluruh atas aktivitas operasional.',
                        'en' => 'Mapping business process flows to give a complete picture of operational activity.',
                    ],
                ],
                2 => [
                    'title' => 'Process Review & Evaluation',
                    'desc' => [
                        'id' => 'Peninjauan dan evaluasi proses bisnis untuk mengidentifikasi kebutuhan dan peluang perbaikan.',
                        'en' => 'Reviewing and evaluating business processes to identify what needs fixing and where the openings are.',
                    ],
                ],
                3 => [
                    'title' => 'Process Improvement',
                    'desc' => [
                        'id' => 'Perumusan rekomendasi untuk meningkatkan efektivitas dan efisiensi proses kerja.',
                        'en' => 'Framing recommendations that raise the effectiveness and efficiency of work processes.',
                    ],
                ],
                4 => [
                    'title' => 'Implementation & Monitoring of Business Procedures',
                    'desc' => [
                        'id' => 'Pendampingan penerapan serta pemantauan prosedur agar berjalan sesuai ketentuan.',
                        'en' => 'Accompanying the rollout of procedures and monitoring that they are followed as set out.',
                    ],
                ],
            ],
        ],
        6 => [
            'slug' => 'services-project-development',
            'title' => 'Project Development',
            'icon' => '■',
            'description' => [
                'id' => 'Tim Project Development IJS mendukung perencanaan dan pelaksanaan proyek bagi unit-unit bisnis dalam grup, mulai dari master planning hingga pengelolaan di lapangan, guna memastikan setiap proyek berjalan sesuai rencana dan anggaran.',
                'en' => 'The IJS project development team supports the planning and delivery of projects for the group’s business units, from master planning through to management on site, so that every project runs to plan and to budget.',
            ],
            'items' => [
                0 => [
                    'title' => 'Master Planning',
                    'desc' => [
                        'id' => 'Penyusunan master plan sebagai dasar perencanaan dan pengembangan proyek.',
                        'en' => 'Drawing up the master plan a project’s planning and development is built on.',
                    ],
                ],
                1 => [
                    'title' => 'Project Design',
                    'desc' => [
                        'id' => 'Pengembangan desain proyek sesuai kebutuhan dan perencanaan yang telah ditetapkan.',
                        'en' => 'Developing project design against the established requirements and plan.',
                    ],
                ],
                2 => [
                    'title' => 'Cost Estimation',
                    'desc' => [
                        'id' => 'Penyusunan estimasi biaya sebagai bagian dari perencanaan proyek.',
                        'en' => 'Building the cost estimate as part of project planning.',
                    ],
                ],
                3 => [
                    'title' => 'Project Planning',
                    'desc' => [
                        'id' => 'Penyusunan rencana kerja proyek, termasuk penjadwalan dan tahapan pelaksanaan.',
                        'en' => 'Setting out the project work plan, including scheduling and delivery stages.',
                    ],
                ],
                4 => [
                    'title' => 'Site Management',
                    'desc' => [
                        'id' => 'Pengelolaan dan pemantauan pelaksanaan proyek di lapangan.',
                        'en' => 'Managing and monitoring project delivery on site.',
                    ],
                ],
            ],
        ],
    ];
}
