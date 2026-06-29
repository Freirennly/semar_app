<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentTemplateCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Illuminate\Support\Facades\DB::table('document_templates')
            ->where('name', 'Proposal Penelitian')
            ->update(['code' => 'PROPOSAL']);

        \Illuminate\Support\Facades\DB::table('document_templates')
            ->where('name', 'Informed Consent Form')
            ->update(['code' => 'ICF']);

        \Illuminate\Support\Facades\DB::table('document_templates')
            ->where('name', 'CV Peneliti')
            ->update(['code' => 'CV']);

        \Illuminate\Support\Facades\DB::table('document_templates')
            ->where('name', 'Surat Pernyataan')
            ->update(['code' => 'STATEMENT']);

        // Backfill any other template names dynamically
        $templates = \Illuminate\Support\Facades\DB::table('document_templates')->whereNull('code')->get();
        foreach ($templates as $t) {
            $code = strtoupper(str_replace('-', '_', \Illuminate\Support\Str::slug($t->name)));
            if (empty($code)) {
                $code = 'TEMP_' . strtoupper(\Illuminate\Support\Str::random(6));
            }
            $originalCode = $code;
            $counter = 1;
            while (\Illuminate\Support\Facades\DB::table('document_templates')->where('code', $code)->exists()) {
                $code = $originalCode . '_' . $counter;
                $counter++;
            }
            \Illuminate\Support\Facades\DB::table('document_templates')->where('id', $t->id)->update(['code' => $code]);
        }
    }
}
