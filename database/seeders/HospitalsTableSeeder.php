<?php

namespace Database\Seeders; // Ini penting!

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HospitalsTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    // Opsional: Cek apakah data sudah ada untuk menghindari duplikasi
    if (DB::table('hospitals')->where('email', 'lwc2074@gmail.com')->doesntExist()) {
      DB::table('hospitals')->insert([
        'name' => 'Lokanthali Wellness Clinic', // Ejaan "Wellness" yang benar
        'slogan' => 'Enhancing Life, Excelling In Care...', // Perbaiki "Incase" menjadi "In Care" dan kapitalisasi
        'logo' => 'uploads/logo.png',
        'address' => 'Lokanthali-1, Madhyapur Thimi, Bhaktpur, Nepal', // Tambahkan spasi setelah koma
        'contact' => '+9779860479432, +9779846288255', // Spasi setelah koma
        'email' => 'lwc2074@gmail.com',
        'pan_no' => '1234567890', // Contoh nomor PAN yang lebih realistis
        'registration_no' => 'REG-' . Str::random(8), // Menggunakan Str::random() untuk nomor registrasi acak
        'website' => 'https://lwc.health.com', // Tambahkan skema URL
        'description' => 'Our motto: healthy life. Lokanthali Wellness Clinic is dedicated to providing comprehensive and compassionate healthcare services.', // Perluas deskripsi
        'tax_type' => 'Health Tax',
        'tax_percent' => 5,
        'invoice_prefix' => 'LWC-',
        'patient_prefix' => 'LWC-',
        'invoice_message' => 'Thank you for choosing Lokanthali Wellness Clinic. We appreciate your trust in our services.', // Pesan invoice yang lebih informatif
        'created_at' => now(), // Tambahkan timestamp
        'updated_at' => now(), // Tambahkan timestamp
      ]);
    } else {
      $this->command->info('Lokanthali Wellness Clinic data already exists. Skipping insertion.');
    }

    // Anda bisa menambahkan entri rumah sakit lain di sini jika diperlukan
    // DB::table('hospitals')->insert([
    //     'name' => 'Nama Rumah Sakit Lain',
    //     'slogan' => 'Slogan RS Lain',
    //     // ... data lainnya
    // ]);
  }
}
