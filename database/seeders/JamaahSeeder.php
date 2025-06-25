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
            ['nama'=>'IMRON / DWI ARISANDI', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'EDY RIYANTO / ASMAUL', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'SUGIONO / UMI HANIK', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'BURHAN / EFRILIE', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'CHASAN / VIVI', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'YANI / SULASTRI', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'ROYAN / DIAN', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'JATMIKO / TIANAH', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'H. MISWANTO / HJ. SUKIMAH', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'KARYONO / SRIYATUN', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'WAHYU / LINA', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'DARMONO / MARYATI', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'DAWUD / VENY', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'MASDUKI / RIYANTI', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'HARYANTO / SUSI', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'MUGIANTO / SUHARTI', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'BUYUNG / TITIN', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'HENDRIK / SUNARSIH', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'ALI / MIA', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'DODIK / TUTIK', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'LILIS / SITI', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'MASUD / DEWI', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'FAQIH / RISNANDA', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'SETYA / VIOLA', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'SUGENG / LISTYORINI', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'MUSTOFA / UMI', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'WAWAN / SHOPIA', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'ANDRE / RIKA', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'SUPRIYONO / SRI', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'FAUZI / BADIAH', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'KRISTIAWAN / MILLA', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'LATIFAN / FINA', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'IKHSAN / NINA', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'ROHMAN / WINDI', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'AGUNG / SEVIRA', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'ALEX / DWI', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'PRIYANTO / WINARNI', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'ARBAI / ANIK', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'BUSRO / IIN', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'BUDI / IS PURWATI', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'YUDI / PURWI', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'JOKO UTOMO / NINGSIH', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'SOFIONO / LILIS', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'ANDRI / LILIS', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'TAJI / LEGIMAH', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'SUDARMONO / ROSI', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'SAIFUL / ANA', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'ERVAN / MAYA', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'HUDA / YUSI', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'BENI', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'HINDUN', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'HARTINI', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'ZULAIKAH', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'AROFAH', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'ZAENAB', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'MUKTI SOLIKAH', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'SITI KALIMAH', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'MUSDAYATI', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'ADINI', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'SOIMAH', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'PARTINI', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'DARWATI', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'SUTANTIN', 'kategori'=>'A', 'status'=>'1'],
            ['nama'=>'MT', 'kategori'=>'A', 'status'=>'1'],
        ];
        Jamaah::insert($jamaah);
    }
}
