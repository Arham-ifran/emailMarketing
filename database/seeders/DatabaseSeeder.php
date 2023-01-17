<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use phpDocumentor\Reflection\Types\This;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ModulesTableSeeder::class);
        $this->call(RightsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(AdminsTableSeeder::class);
        $this->call(EmailTemplatesTableSeeder::class);
        $this->call(CmsPagesTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(FaqsTableSeeder::class);
        $this->call(HomeContentsTableSeeder::class);
        $this->call(ServicesTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(PackageFeaturesSeeder::class);
        $this->call(PackagesSeeder::class);
        $this->call(PaymentGatewaySettingsTableSeeder::class);
        // \App\Models\User::factory(10)->create();
        $this->call(AboutUsContentsSeeder::class);
        $this->call(AboutUsTestimonialsSeeder::class);
        $this->call(FeaturesSeeder::class);
        $this->call(HomeContentsSeeder::class);
        $this->call(PackageSettingsSeeder::class);
        $this->call(ServicesSeeder::class);
        $this->call(TestimonialsSeeder::class);
        $this->call(TimezonesSeeder::class);
    }
}
