<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('password');
            $table->enum('role', ['admin', 'staff'])->default('staff');
            $table->timestamps();
        });

        // Default admin user
        User::create([
            'name' => 'Admin',
            'password' => Hash::make('admin@admin'),
            'role' => 'admin',
        ]);

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('made')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('location');
            $table->date('start_date');
            $table->decimal('goal_amount', 20, 0);
            $table->decimal('total_donations', 20, 0)->default(0);
            $table->decimal('total_expenses', 20, 0)->default(0);
            $table->decimal('balance', 20, 0)->default(0);
            $table->date('end_date');
            $table->timestamps();
        });

        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('made')->nullable()->constrained('users')->onDelete('set null');
            $table->string('name')->nullable();
            $table->decimal('amount', 20, 0);
            $table->enum('payment_method', ['FIB', 'Fastpay','Cash','Other']); // E.g., FIB, Fastpay
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('made')->nullable()->constrained('users')->onDelete('set null');
            $table->string('name');
            $table->foreignId('category')->nullable()->constrained('categories')->onDelete('set null');
            $table->text('description')->nullable();
            $table->decimal('price', 20, 0);
            $table->integer('quantity');
            $table->decimal('total', 20, 0);
            $table->enum('payment_method', ['FIB', 'Fastpay','Cash','Other']); // E.g., FIB, Fastpay
            $table->decimal('paid', 20, 0);
            $table->string('invoice')->nullable();
            $table->enum('status', ['paid', 'unpaid'])->default('unpaid');
            $table->timestamps();
        });

       
        //Case Table        
        Schema::create('cases', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('made')->nullable()->constrained('users')->onDelete('set null');
            $table->string('name');
            $table->string('phone');
            $table->string('address');
            $table->unsignedBigInteger('case_id')->nullable()->constrained('cases')->onDelete('set null');
            $table->decimal('aid', 20, 0)->default(0);
            $table->date('date_received')->nullable();
            $table->timestamps();
        });

           // Debts Table (For Loans & Unpaid Amounts)
           Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expense_id')->nullable();
            $table->decimal('paid', 20, 0)->default(0.00); // Partial payments
            $table->date('due_date');
            $table->timestamps();
            $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('debts');
        Schema::dropIfExists('people');
        Schema::dropIfExists('cases');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('donations');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('users');
    }
};
