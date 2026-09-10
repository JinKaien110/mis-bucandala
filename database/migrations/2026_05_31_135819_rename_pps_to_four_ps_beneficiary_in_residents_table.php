<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     /**
      * Run the migrations.
      */
     public function up(): void
     {
         Schema::table('residents', function (Blueprint $table) {
             if (Schema::hasColumn('residents', 'pps')) {
                 $table->renameColumn('pps', 'four_ps_beneficiary');
             }
         });
     }

     /**
      * Reverse the migrations.
      */
     public function down(): void
     {
         Schema::table('residents', function (Blueprint $table) {
             if (Schema::hasColumn('residents', 'four_ps_beneficiary')) {
                 $table->renameColumn('four_ps_beneficiary', 'pps');
             }
         });
     }
};
