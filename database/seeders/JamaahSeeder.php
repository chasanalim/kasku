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
            ['nama' => 'IMRON / DWI ARISANDI', 'kategori' => 'A', 'status' => '1'],
            ['nama' => 'EDY RIYANTO / ASMAUL', 'kategori' => 'A', 'status' => '1'],
            ['nama' => 'SUGIONO / UMI HANIK', 'kategori' => 'A', 'status' => '1'],
            ['nama' => 'BURHAN / EFRILIE', 'kategori' => 'B', 'status' => '1'],
            ['nama' => 'CHASAN / VIVI', 'kategori' => 'B', 'status' => '1'],
            ['nama' => 'YANI / SULASTRI', 'kategori' => 'B', 'status' => '1'],
            ['nama' => 'ROYAN / DIAN', 'kategori' => 'B', 'status' => '1'],
            ['nama' => 'JATMIKO / TIANAH', 'kategori' => 'C', 'status' => '1'],
            ['nama' => 'H. MISWANTO / HJ. SUKIMAH', 'kategori' => 'C', 'status' => '1'],
            ['nama' => 'KARYONO / SRIYATUN', 'kategori' => 'B', 'status' => '1'],
            ['nama' => 'WAHYU / LINA', 'kategori' => 'A', 'status' => '1'],
            ['nama' => 'DARMONO / MARYATI', 'kategori' => 'A', 'status' => '1'],
            ['nama' => 'DAWUD / VENY', 'kategori' => 'A', 'status' => '1'],
            ['nama' => 'MASDUKI / RIYANTI', 'kategori' => 'A', 'status' => '1'],
            ['nama' => 'HARYANTO / SUSI', 'kategori' => 'A', 'status' => '1'],
            ['nama' => 'MUGIANTO / SUHARTI', 'kategori' => 'B', 'status' => '1'],
            ['nama' => 'BUYUNG / TITIN', 'kategori' => 'A', 'status' => '1'],
            ['nama' => 'HENDRIK / SUNARSIH', 'kategori' => 'B', 'status' => '1'],
            ['nama' => 'ALI / MIA', 'kategori' => 'A', 'status' => '1'],
            ['nama' => 'DODIK / TUTIK', 'kategori' => 'A', 'status' => '1'],
            ['nama' => 'LILIS / SITI', 'kategori' => 'C', 'status' => '1'],
            ['nama' => 'MASUD / DEWI', 'kategori' => 'B', 'status' => '1'],
            ['nama' => 'FAQIH / RISNANDA', 'kategori' => 'A', 'status' => '1'],
            ['nama' => 'SETYA / VIOLA', 'kategori' => 'B', 'status' => '1'],
            ['nama' => 'SUGENG / LISTYORINI', 'kategori' => 'B', 'status' => '1'],
            ['nama' => 'MUSTOFA / UMI', 'kategori' => 'B', 'status' => '1'],
            ['nama' => 'WAWAN / SHOPIA', 'kategori' => 'B', 'status' => '1'],
            ['nama' => 'ANDRE / RIKA', 'kategori' => 'C', 'status' => '1'],
            ['nama' => 'SUPRIYONO / SRI', 'kategori' => 'B', 'status' => '1'],
            ['nama' => 'FAUZI / BADIAH', 'kategori' => 'C', 'status' => '1'],
            ['nama' => 'KRISTIAWAN / MILLA', 'kategori' => 'C', 'status' => '1'],
            ['nama' => 'LATIFAN / FINA', 'kategori' => 'B', 'status' => '1'],
            ['nama' => 'IKHSAN / NINA', 'kategori' => 'C', 'status' => '1'],
            ['nama' => 'ROHMAN / WINDI', 'kategori' => 'C', 'status' => '1'],
            ['nama' => 'AGUNG / SEVIRA', 'kategori' => 'C', 'status' => '1'],
            ['nama' => 'ALEX / DWI', 'kategori' => 'C', 'status' => '1'],
            ['nama' => 'PRIYANTO / WINARNI', 'kategori' => 'B', 'status' => '1'],
            ['nama' => 'ARBAI / ANIK', 'kategori' => 'C', 'status' => '1'],
            ['nama' => 'BUSRO / IIN', 'kategori' => 'B', 'status' => '1'],
            ['nama' => 'BUDI / IS PURWATI', 'kategori' => 'C', 'status' => '1'],
            ['nama' => 'YUDI / PURWI', 'kategori' => 'C', 'status' => '1'],
            ['nama' => 'JOKO UTOMO / NINGSIH', 'kategori' => 'C', 'status' => '1'],
            ['nama' => 'SOFIONO / LILIS', 'kategori' => 'B', 'status' => '1'],
            ['nama' => 'ANDRI / LILIS', 'kategori' => 'C', 'status' => '1'],
            ['nama' => 'TAJI / LEGIMAH', 'kategori' => 'C', 'status' => '1'],
            ['nama' => 'SUDARMONO / ROSI', 'kategori' => 'C', 'status' => '1'],
            ['nama' => 'SAIFUL / ANA', 'kategori' => 'C', 'status' => '1'],
            ['nama' => 'ERVAN / MAYA', 'kategori' => 'C', 'status' => '1'],
            ['nama' => 'HUDA / YUSI', 'kategori' => 'C', 'status' => '1'],
            ['nama' => 'BENI', 'kategori' => 'D', 'status' => '1'],
            ['nama' => 'HINDUN', 'kategori' => 'D', 'status' => '1'],
            ['nama' => 'HARTINI', 'kategori' => 'D', 'status' => '1'],
            ['nama' => 'ZULAIKAH', 'kategori' => 'D', 'status' => '1'],
            ['nama' => 'AROFAH', 'kategori' => 'D', 'status' => '1'],
            ['nama' => 'ZAENAB', 'kategori' => 'D', 'status' => '1'],
            ['nama' => 'MUKTI SOLIKAH', 'kategori' => 'D', 'status' => '1'],
            ['nama' => 'SITI KALIMAH', 'kategori' => 'D', 'status' => '1'],
            ['nama' => 'MUSDAYATI', 'kategori' => 'D', 'status' => '1'],
            ['nama' => 'ADINI', 'kategori' => 'D', 'status' => '1'],
            ['nama' => 'SOIMAH', 'kategori' => 'D', 'status' => '1'],
            ['nama' => 'PARTINI', 'kategori' => 'D', 'status' => '1'],
            ['nama' => 'DARWATI', 'kategori' => 'D', 'status' => '1'],
            ['nama' => 'SUTANTIN', 'kategori' => 'D', 'status' => '1'],
            ['nama' => 'MT', 'kategori' => 'C', 'status' => '1'],
            ['nama' => 'ARI', 'kategori' => 'C', 'status' => '1'],
        ];
        Jamaah::insert($jamaah);
    }
}
