<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * 1. Tahun '77' pada "Kejadian Bencana" PEJAWARAN sebenarnya adalah
     *    record tahun 2025 yang tahunnya korup (nilai 0.0 = nol bencana),
     *    sehingga PEJAWARAN hilang dari data Kejadian Bencana 2025.
     * 2. Penulisan kecamatan diseragamkan ke ejaan resmi "PURWAREJA KLAMPOK".
     * 3. Kolom "tahun" pada bantuan_sosials dan indikator_kecamatans
     *    diseragamkan menjadi INTEGER (sebelumnya VARCHAR/TEXT).
     */
    public function up(): void
    {
        // Fix 1 — kembalikan tahun korup '77' menjadi '2025' (PEJAWARAN, Kejadian Bencana).
        DB::statement(
            "UPDATE indikator_kecamatans SET tahun = '2025'
             WHERE tahun = '77' AND kecamatan = 'PEJAWARAN' AND nama_indikator = 'Kejadian Bencana'"
        );

        // Fix 2 — seragamkan ejaan kecamatan PURWOREJA -> PURWAREJA (ejaan resmi).
        DB::statement(
            "UPDATE kesejahteraan_kecamatans SET kecamatan = 'PURWAREJA KLAMPOK'
             WHERE kecamatan = 'PURWOREJA KLAMPOK'"
        );

        // Fix 3a — bantuan_sosials: tahun VARCHAR -> INTEGER (recreate table).
        DB::statement('CREATE TABLE "bantuan_sosials_new" (
            "id" integer primary key autoincrement not null,
            "tahun" integer not null,
            "kecamatan" varchar not null,
            "jumlah_penerima" integer not null,
            "anggaran_ribu_rupiah" integer not null,
            "created_at" datetime,
            "updated_at" datetime
        )');
        DB::statement('INSERT INTO "bantuan_sosials_new"
            ("id", "tahun", "kecamatan", "jumlah_penerima", "anggaran_ribu_rupiah", "created_at", "updated_at")
            SELECT "id", CAST("tahun" AS INTEGER), "kecamatan", "jumlah_penerima", "anggaran_ribu_rupiah", "created_at", "updated_at"
            FROM "bantuan_sosials"');
        DB::statement('DROP TABLE "bantuan_sosials"');
        DB::statement('ALTER TABLE "bantuan_sosials_new" RENAME TO "bantuan_sosials"');

        // Fix 3b — indikator_kecamatans: tahun VARCHAR -> INTEGER (recreate table).
        DB::statement('CREATE TABLE "indikator_kecamatans_new" (
            "id" integer primary key autoincrement not null,
            "tahun" integer not null,
            "kecamatan" varchar not null,
            "kategori" varchar not null,
            "nama_indikator" varchar not null,
            "nilai" double not null,
            "created_at" datetime,
            "updated_at" datetime
        )');
        DB::statement('INSERT INTO "indikator_kecamatans_new"
            ("id", "tahun", "kecamatan", "kategori", "nama_indikator", "nilai", "created_at", "updated_at")
            SELECT "id", CAST("tahun" AS INTEGER), "kecamatan", "kategori", "nama_indikator", "nilai", "created_at", "updated_at"
            FROM "indikator_kecamatans"');
        DB::statement('DROP TABLE "indikator_kecamatans"');
        DB::statement('ALTER TABLE "indikator_kecamatans_new" RENAME TO "indikator_kecamatans"');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse 3a — bantuan_sosials kembali ke VARCHAR tahun.
        DB::statement('CREATE TABLE "bantuan_sosials_old" (
            "id" integer primary key autoincrement not null,
            "tahun" varchar not null,
            "kecamatan" varchar not null,
            "jumlah_penerima" integer not null,
            "anggaran_ribu_rupiah" integer not null,
            "created_at" datetime,
            "updated_at" datetime
        )');
        DB::statement('INSERT INTO "bantuan_sosials_old"
            ("id", "tahun", "kecamatan", "jumlah_penerima", "anggaran_ribu_rupiah", "created_at", "updated_at")
            SELECT "id", CAST("tahun" AS TEXT), "kecamatan", "jumlah_penerima", "anggaran_ribu_rupiah", "created_at", "updated_at"
            FROM "bantuan_sosials"');
        DB::statement('DROP TABLE "bantuan_sosials"');
        DB::statement('ALTER TABLE "bantuan_sosials_old" RENAME TO "bantuan_sosials"');

        // Reverse 3b — indikator_kecamatans kembali ke VARCHAR tahun.
        DB::statement('CREATE TABLE "indikator_kecamatans_old" (
            "id" integer primary key autoincrement not null,
            "tahun" varchar not null,
            "kecamatan" varchar not null,
            "kategori" varchar not null,
            "nama_indikator" varchar not null,
            "nilai" double not null,
            "created_at" datetime,
            "updated_at" datetime
        )');
        DB::statement('INSERT INTO "indikator_kecamatans_old"
            ("id", "tahun", "kecamatan", "kategori", "nama_indikator", "nilai", "created_at", "updated_at")
            SELECT "id", CAST("tahun" AS TEXT), "kecamatan", "kategori", "nama_indikator", "nilai", "created_at", "updated_at"
            FROM "indikator_kecamatans"');
        DB::statement('DROP TABLE "indikator_kecamatans"');
        DB::statement('ALTER TABLE "indikator_kecamatans_old" RENAME TO "indikator_kecamatans"');

        // Reverse 2 — ejaan kembali ke PURWOREJA.
        DB::statement(
            "UPDATE kesejahteraan_kecamatans SET kecamatan = 'PURWOREJA KLAMPOK'
             WHERE kecamatan = 'PURWAREJA KLAMPOK'"
        );

        // Reverse 1 — tahun kembali ke '77'.
        DB::statement(
            "UPDATE indikator_kecamatans SET tahun = '77'
             WHERE tahun = 2025 AND kecamatan = 'PEJAWARAN' AND nama_indikator = 'Kejadian Bencana'"
        );
    }
};