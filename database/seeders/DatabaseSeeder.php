<?php

namespace Database\Seeders;

use App\Models\Aggregator;
use App\Models\AggregatorStock;
use App\Models\Collection;
use App\Models\Recycler;
use App\Models\Shipment;
use App\Models\User;
use App\Models\WasteUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Roles ─────────────────────────────────────────────────
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        $adminRole      = Role::firstOrCreate(['name' => 'admin',      'guard_name' => 'web']);

        // ── Users ─────────────────────────────────────────────────
        $superadmin = User::firstOrCreate(['email' => 'superadmin@sirsak.id'], [
            'name'     => 'Super Administrator',
            'password' => Hash::make('superadmin123'),
            'is_active' => true,
        ]);
        $superadmin->assignRole($superadminRole);

        $admin = User::firstOrCreate(['email' => 'admin@sirsak.id'], [
            'name'      => 'Administrator',
            'password'  => Hash::make('admin123'),
            'is_active' => true,
        ]);
        $admin->assignRole($adminRole);

        // ── Aggregators ───────────────────────────────────────────
        $agg1 = Aggregator::firstOrCreate(['code' => 'AGG01'], [
            'name' => 'BSI Kota Surabaya', 'pic_name' => 'Budi Santoso',
            'village' => 'Wonokromo', 'district' => 'Wonokromo', 'regency' => 'Surabaya',
            'phone' => '08111000001', 'latitude' => -7.2830, 'longitude' => 112.7460, 'is_active' => true,
        ]);
        $agg2 = Aggregator::firstOrCreate(['code' => 'AGG02'], [
            'name' => 'BSI Kota Mojokerto', 'pic_name' => 'Siti Rahayu',
            'village' => 'Puri', 'district' => 'Puri', 'regency' => 'Mojokerto',
            'phone' => '08111000002', 'latitude' => -7.4700, 'longitude' => 112.4330, 'is_active' => true,
        ]);
        $agg3 = Aggregator::firstOrCreate(['code' => 'AGG03'], [
            'name' => 'BSI Kota Pasuruan', 'pic_name' => 'Agus Wibowo',
            'village' => 'Bugul Kidul', 'district' => 'Bugul Kidul', 'regency' => 'Pasuruan',
            'phone' => '08111000003', 'latitude' => -7.6450, 'longitude' => 112.9100, 'is_active' => true,
        ]);
        $agg4 = Aggregator::firstOrCreate(['code' => 'AGG04'], [
            'name' => 'BSI Kota Malang', 'pic_name' => 'Dewi Lestari',
            'village' => 'Sukun', 'district' => 'Sukun', 'regency' => 'Malang',
            'phone' => '08111000004', 'latitude' => -7.9870, 'longitude' => 112.6270, 'is_active' => true,
        ]);

        // ── Recyclers ─────────────────────────────────────────────
        $rec1 = Recycler::firstOrCreate(['code' => 'REC01'], [
            'name' => 'PT Bumi Indus Recycle', 'company_type' => 'PT',
            'pic_name' => 'Hendra Wijaya', 'address' => 'Jl. Industri No. 15, Rungkut',
            'regency' => 'Surabaya', 'phone' => '03112345001',
            'latitude' => -7.3200, 'longitude' => 112.7800, 'is_active' => true,
        ]);
        $rec2 = Recycler::firstOrCreate(['code' => 'REC02'], [
            'name' => 'PT Veolia Waste Solutions', 'company_type' => 'PT',
            'pic_name' => 'Lisa Permata', 'address' => 'Jl. Raya Gedangan No. 8',
            'regency' => 'Sidoarjo', 'phone' => '03112345002',
            'latitude' => -7.4100, 'longitude' => 112.7100, 'is_active' => true,
        ]);

        // ── Waste Units (BSU) ────────────────────────────────────
        $bsuData = [
            ['BSU01', $agg1->id, 'BSU Wonokromo',   'Wonokromo',   'Wonokromo',   'Surabaya',  '08222000001', -7.2950, 112.7380],
            ['BSU02', $agg1->id, 'BSU Gubeng',       'Gubeng',      'Gubeng',      'Surabaya',  '08222000002', -7.2650, 112.7520],
            ['BSU03', $agg2->id, 'BSU Puri',         'Puri',        'Puri',        'Mojokerto', '08222000003', -7.4650, 112.4300],
            ['BSU04', $agg2->id, 'BSU Sooko',        'Sooko',       'Sooko',       'Mojokerto', '08222000004', -7.4800, 112.4450],
            ['BSU05', $agg3->id, 'BSU Bugul Kidul',  'Bugul Kidul', 'Bugul Kidul', 'Pasuruan',  '08222000005', -7.6400, 112.9050],
            ['BSU06', $agg3->id, 'BSU Gadingrejo',   'Gadingrejo',  'Gadingrejo',  'Pasuruan',  '08222000006', -7.6550, 112.9200],
            ['BSU07', $agg4->id, 'BSU Sukun',        'Sukun',       'Sukun',       'Malang',    '08222000007', -7.9920, 112.6180],
            ['BSU08', $agg4->id, 'BSU Lowokwaru',    'Lowokwaru',   'Lowokwaru',   'Malang',    '08222000008', -7.9500, 112.6150],
        ];
        $bsuModels = [];
        foreach ($bsuData as [$code, $aggId, $name, $vil, $dis, $reg, $phone, $lat, $lng]) {
            $bsuModels[$code] = WasteUnit::firstOrCreate(['code' => $code], [
                'aggregator_id' => $aggId, 'name' => $name, 'village' => $vil,
                'district' => $dis, 'regency' => $reg, 'phone' => $phone,
                'latitude' => $lat, 'longitude' => $lng, 'is_active' => true, 'joined_at' => '2021-01-01',
            ]);
        }

        // ── Sample Collections ────────────────────────────────────
        $materials = ['PET', 'MLP', 'Kardus', 'Metal', 'HDPE'];
        $conditions = ['Bersih & Kering', 'Kotor / Campuran'];
        $prices = ['PET' => 5000, 'MLP' => 3000, 'Kardus' => 2000, 'Metal' => 8000, 'HDPE' => 4000];

        $bsuList   = array_values($bsuModels);
        $aggList   = [$agg1, $agg2, $agg3, $agg4];
        $startDate = now()->subYears(4)->startOfYear();

        for ($m = 0; $m <= 48; $m++) {
            $monthDate = $startDate->copy()->addMonths($m);
            if ($monthDate->isFuture()) break;

            foreach ($bsuList as $bsu) {
                foreach ($materials as $mat) {
                    if (rand(0, 2) === 0) continue; // skip some randomly
                    $gross = rand(50, 400);
                    $tare  = rand(5, 20);
                    $net   = $gross - $tare;
                    $price = $prices[$mat];
                    Collection::firstOrCreate(
                        ['transaction_code' => 'WC-' . $monthDate->format('Ym') . '-' . $bsu->code . '-' . $mat . '-' . rand(100, 999)],
                        [
                            'waste_unit_id'      => $bsu->id,
                            'aggregator_id'      => $bsu->aggregator_id,
                            'recorded_by'        => $superadmin->id,
                            'material_type'      => $mat,
                            'material_condition' => $conditions[array_rand($conditions)],
                            'gross_weight_kg'    => $gross,
                            'tare_weight_kg'     => $tare,
                            'net_weight_kg'      => $net,
                            'price_per_kg'       => $price,
                            'total_value'        => $net * $price,
                            'collected_at'       => $monthDate->copy()->addDays(rand(1, 25)),
                        ]
                    );
                }
            }
        }

        // ── Aggregator Stocks (rebuild) ───────────────────────────
        AggregatorStock::truncate();
        foreach ($aggList as $agg) {
            foreach ($materials as $mat) {
                $total = Collection::where('aggregator_id', $agg->id)
                    ->where('material_type', $mat)->sum('net_weight_kg');
                if ($total > 0) {
                    AggregatorStock::create([
                        'aggregator_id'  => $agg->id,
                        'material_type'  => $mat,
                        'stock_kg'       => $total,
                        'last_updated_at' => now(),
                    ]);
                }
            }
        }

        // ── Sample Shipments ──────────────────────────────────────
        for ($m = 0; $m <= 48; $m += 2) {
            $monthDate = $startDate->copy()->addMonths($m);
            if ($monthDate->isFuture()) break;

            Shipment::firstOrCreate(['shipment_code' => 'SHP-' . $monthDate->format('Ym') . '-0001'], [
                'aggregator_id'     => $agg1->id, 'recycler_id' => $rec1->id,
                'dispatched_by'     => $superadmin->id, 'confirmed_by' => $superadmin->id,
                'material_type'     => 'PET', 'shipped_weight_kg' => rand(300, 500), 'received_weight_kg' => rand(290, 490),
                'status'            => 'received', 'vehicle_info' => 'B 1234 ABC',
                'dispatched_at'     => $monthDate->copy()->addDays(5), 'received_at' => $monthDate->copy()->addDays(6),
            ]);
            Shipment::firstOrCreate(['shipment_code' => 'SHP-' . $monthDate->format('Ym') . '-0002'], [
                'aggregator_id'     => $agg3->id, 'recycler_id' => $rec2->id,
                'dispatched_by'     => $superadmin->id, 'confirmed_by' => $superadmin->id,
                'material_type'     => 'PET', 'shipped_weight_kg' => rand(150, 250), 'received_weight_kg' => rand(140, 240),
                'status'            => 'received', 'vehicle_info' => 'L 5678 DEF',
                'dispatched_at'     => $monthDate->copy()->addDays(15), 'received_at' => $monthDate->copy()->addDays(16),
            ]);
            Shipment::firstOrCreate(['shipment_code' => 'SHP-' . $monthDate->format('Ym') . '-0003'], [
                'aggregator_id' => $agg2->id, 'recycler_id' => $rec1->id,
                'dispatched_by' => $superadmin->id, 'confirmed_by' => $superadmin->id,
                'material_type' => 'MLP', 'shipped_weight_kg' => rand(100, 200), 'received_weight_kg' => rand(90, 190),
                'status' => 'received', 'vehicle_info' => 'W 9999 GHI',
                'dispatched_at' => $monthDate->copy()->addDays(20), 'received_at' => $monthDate->copy()->addDays(22),
            ]);
        }

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('SuperAdmin: superadmin@sirsak.id / superadmin123');
        $this->command->info('Admin:      admin@sirsak.id / admin123');
    }
}
