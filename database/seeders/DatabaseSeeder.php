<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{Cases, Debt, User, Category, Project, Donation, People, Expense};
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(20)->create();
        Category::factory(3)->create();
        Project::factory(5)->create();
        Donation::factory(10)->create();
        Expense::factory(10)->create();
        Cases::factory(20)->create();
        People::factory(20)->create();
        Debt::factory(20)->create();
    }
}
