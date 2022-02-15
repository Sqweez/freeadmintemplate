<?php

use App\MarginType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarginTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('margin_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('partner_cashback_rules')->nullable();
            $table->text('salary_rules')->nullable();
            $table->timestamps();
        });

        MarginType::create(
            [
                'title' => 'A',
                'partner_cashback_rules' => [
                    [
                        'threshold' => 0,
                        'value' => 10,
                    ],
                    [
                        'threshold' => 50000,
                        'value' => 12
                    ],
                    [
                        'threshold' => 100000,
                        'value' => 14
                    ]
                ],
            ]
        );

        MarginType::create([
            'title' => 'B',
            'partner_cashback_rules' => [
                [
                    'threshold' => 0,
                    'value' => 3,
                ],
                [
                    'threshold' => 50000,
                    'value' => 5
                ],
                [
                    'threshold' => 100000,
                    'value' => 7
                ]
            ],
        ]);

        MarginType::create([
            'title' => 'C',
            'partner_cashback_rules' => [
                    [
                        'threshold' => 0,
                        'value' => 3,
                    ],
                    [
                        'threshold' => 50000,
                        'value' => 5
                    ],
                    [
                        'threshold' => 100000,
                        'value' => 7
                    ]
                ]
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('margin_types');
    }
}
