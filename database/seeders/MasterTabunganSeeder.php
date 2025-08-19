<?php

namespace Database\Seeders;

use App\Models\MasterTabungan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterTabunganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jamaah = [
            ['nama' => 'IMRON / DWI ARISANDI', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'EDY RIYANTO / ASMAUL', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'SUGIONO / UMI HANIK', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'BURHAN / EFRILIE', 'jatah' => '500000', 'status' => '1'],
            ['nama' => 'CHASAN / VIVI', 'jatah' => '500000', 'status' => '1'],
            ['nama' => 'YANI / SULASTRI', 'jatah' => '500000', 'status' => '1'],
            ['nama' => 'ROYAN / DIAN', 'jatah' => '500000', 'status' => '1'],
            ['nama' => 'JATMIKO / TIANAH', 'jatah' => '200000', 'status' => '1'],
            ['nama' => 'H. MISWANTO / HJ. SUKIMAH', 'jatah' => '200000', 'status' => '1'],
            ['nama' => 'KARYONO / SRIYATUN', 'jatah' => '500000', 'status' => '1'],
            ['nama' => 'WAHYU / LINA', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'DARMONO / MARYATI', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'DAWUD / VENY', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'MASDUKI / RIYANTI', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'HARYANTO / SUSI', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'MUGIANTO / SUHARTI', 'jatah' => '500000', 'status' => '1'],
            ['nama' => 'BUYUNG / TITIN', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'HENDRIK / SUNARSIH', 'jatah' => '500000', 'status' => '1'],
            ['nama' => 'ALI / MIA', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'DODIK / TUTIK', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'LILIS / SITI', 'jatah' => '200000', 'status' => '1'],
            ['nama' => 'MASUD / DEWI', 'jatah' => '500000', 'status' => '1'],
            ['nama' => 'FAQIH / RISNANDA', 'jatah' => '1000000', 'status' => '1'],
            ['nama' => 'SETYA / VIOLA', 'jatah' => '500000', 'status' => '1'],
            ['nama' => 'SUGENG / LISTYORINI', 'jatah' => '500000', 'status' => '1'],
            ['nama' => 'MUSTOFA / UMI', 'jatah' => '500000', 'status' => '1'],
            ['nama' => 'WAWAN / SHOPIA', 'jatah' => '500000', 'status' => '1'],
            ['nama' => 'ANDRE / RIKA', 'jatah' => '200000', 'status' => '1'],
            ['nama' => 'SUPRIYONO / SRI', 'jatah' => '500000', 'status' => '1'],
            ['nama' => 'FAUZI / BADIAH', 'jatah' => '200000', 'status' => '1'],
            ['nama' => 'KRISTIAWAN / MILLA', 'jatah' => '200000', 'status' => '1'],
            ['nama' => 'LATIFAN / FINA', 'jatah' => '500000', 'status' => '1'],
            ['nama' => 'IKHSAN / NINA', 'jatah' => '200000', 'status' => '1'],
            ['nama' => 'ROHMAN / WINDI', 'jatah' => '200000', 'status' => '1'],
            ['nama' => 'AGUNG / SEVIRA', 'jatah' => '200000', 'status' => '1'],
            ['nama' => 'ALEX / DWI', 'jatah' => '200000', 'status' => '1'],
            ['nama' => 'PRIYANTO / WINARNI', 'jatah' => '500000', 'status' => '1'],
            ['nama' => 'ARBAI / ANIK', 'jatah' => '200000', 'status' => '1'],
            ['nama' => 'BUSRO / IIN', 'jatah' => '500000', 'status' => '1'],
            ['nama' => 'BUDI / IS PURWATI', 'jatah' => '200000', 'status' => '1'],
            ['nama' => 'YUDI / PURWI', 'jatah' => '200000', 'status' => '1'],
            ['nama' => 'JOKO UTOMO / NINGSIH', 'jatah' => '200000', 'status' => '1'],
            ['nama' => 'SOFIONO / LILIS', 'jatah' => '500000', 'status' => '1'],
            ['nama' => 'ANDRI / LILIS', 'jatah' => '200000', 'status' => '1'],
            ['nama' => 'TAJI / LEGIMAH', 'jatah' => '200000', 'status' => '1'],
            ['nama' => 'SUDARMONO / ROSI', 'jatah' => '200000', 'status' => '1'],
            ['nama' => 'SAIFUL / ANA', 'jatah' => '200000', 'status' => '1'],
            ['nama' => 'ERVAN / MAYA', 'jatah' => '200000', 'status' => '1'],
            ['nama' => 'HUDA / YUSI', 'jatah' => '200000', 'status' => '1'],
            ['nama' => 'BENI', 'jatah' => '100000', 'status' => '1'],
            ['nama' => 'HINDUN', 'jatah' => '100000', 'status' => '1'],
            ['nama' => 'HARTINI', 'jatah' => '100000', 'status' => '1'],
            ['nama' => 'ZULAIKAH', 'jatah' => '100000', 'status' => '1'],
            ['nama' => 'AROFAH', 'jatah' => '100000', 'status' => '1'],
            ['nama' => 'ZAENAB', 'jatah' => '100000', 'status' => '1'],
            ['nama' => 'MUKTI SOLIKAH', 'jatah' => '100000', 'status' => '1'],
            ['nama' => 'SITI KALIMAH', 'jatah' => '100000', 'status' => '1'],
            ['nama' => 'MUSDAYATI', 'jatah' => '100000', 'status' => '1'],
            ['nama' => 'ADINI', 'jatah' => '100000', 'status' => '1'],
            ['nama' => 'SOIMAH', 'jatah' => '100000', 'status' => '1'],
            ['nama' => 'PARTINI', 'jatah' => '100000', 'status' => '1'],
            ['nama' => 'DARWATI', 'jatah' => '100000', 'status' => '1'],
            ['nama' => 'SUTANTIN', 'jatah' => '100000', 'status' => '1'],
            ['nama' => 'MT', 'jatah' => '200000', 'status' => '1'],
            ['nama' => 'ARI', 'jatah' => '200000', 'status' => '1'],
        ];
        MasterTabungan::insert($jamaah);
    }
}
