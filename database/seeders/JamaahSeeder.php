<?php

namespace Database\Seeders;

use App\Models\Jamaah;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class JamaahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jamaah = [
            ['nama' => 'IMRON / DWI ARISANDI', 'kategori' => 'A', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'EDY RIYANTO / ASMAUL', 'kategori' => 'A', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'SUGIONO / UMI HANIK', 'kategori' => 'A', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'BURHAN / EFRILIE', 'kategori' => 'B', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'CHASAN / VIVI', 'kategori' => 'B', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'YANI / SULASTRI', 'kategori' => 'B', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'ROYAN / DIAN', 'kategori' => 'B', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'JATMIKO / TIANAH', 'kategori' => 'C', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'H. MISWANTO / HJ. SUKIMAH', 'kategori' => 'C', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'KARYONO / SRIYATUN', 'kategori' => 'B', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'WAHYU / LINA', 'kategori' => 'A', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'DARMONO / MARYATI', 'kategori' => 'A', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'DAWUD / VENY', 'kategori' => 'A', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'MASDUKI / RIYANTI', 'kategori' => 'A', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'HARYANTO / SUSI', 'kategori' => 'A', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'MUGIANTO / SUHARTI', 'kategori' => 'B', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'BUYUNG / TITIN', 'kategori' => 'A', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'HENDRIK / SUNARSIH', 'kategori' => 'B', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'ALI / MIA', 'kategori' => 'A', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'DODIK / TUTIK', 'kategori' => 'A', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'LILIS / SITI', 'kategori' => 'C', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'MASUD / DEWI', 'kategori' => 'B', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'FAQIH / RISNANDA', 'kategori' => 'A', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'SETYA / VIOLA', 'kategori' => 'B', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'SUGENG / LISTYORINI', 'kategori' => 'B', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'MUSTOFA / UMI', 'kategori' => 'B', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'WAWAN / SHOPIA', 'kategori' => 'B', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'ANDRE / RIKA', 'kategori' => 'C', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'SUPRIYONO / SRI', 'kategori' => 'B', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'FAUZI / BADIAH', 'kategori' => 'C', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'KRISTIAWAN / MILLA', 'kategori' => 'C', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'LATIFAN / FINA', 'kategori' => 'B', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'IKHSAN / NINA', 'kategori' => 'C', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'ROHMAN / WINDI', 'kategori' => 'C', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'AGUNG / SEVIRA', 'kategori' => 'C', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'ALEX / DWI', 'kategori' => 'C', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'PRIYANTO / WINARNI', 'kategori' => 'B', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'ARBAI / ANIK', 'kategori' => 'C', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'BUSRO / IIN', 'kategori' => 'B', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'BUDI / IS PURWATI', 'kategori' => 'C', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'YUDI / PURWI', 'kategori' => 'C', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'JOKO UTOMO / NINGSIH', 'kategori' => 'C', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'SOFIONO / LILIS', 'kategori' => 'B', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'ANDRI / LILIS', 'kategori' => 'C', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'TAJI / LEGIMAH', 'kategori' => 'C', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'SUDARMONO / ROSI', 'kategori' => 'C', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'SAIFUL / ANA', 'kategori' => 'C', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'ERVAN / MAYA', 'kategori' => 'C', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'HUDA / YUSI', 'kategori' => 'C', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'BENI', 'kategori' => 'D', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'HINDUN', 'kategori' => 'D', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'HARTINI', 'kategori' => 'D', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'ZULAIKAH', 'kategori' => 'D', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'AROFAH', 'kategori' => 'D', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'ZAENAB', 'kategori' => 'D', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'MUKTI SOLIKAH', 'kategori' => 'D', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'SITI KALIMAH', 'kategori' => 'D', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'MUSDAYATI', 'kategori' => 'D', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'ADINI', 'kategori' => 'D', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'SOIMAH', 'kategori' => 'D', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'PARTINI', 'kategori' => 'D', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'DARWATI', 'kategori' => 'D', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'SUTANTIN', 'kategori' => 'D', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'MT', 'kategori' => 'C', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'ARI', 'kategori' => 'C', 'jatah' => '1000000', 'status' => '1'],
        ];
        Jamaah::insert($jamaah);
    }
}
