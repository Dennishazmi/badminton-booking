<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Court;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin Account ──────────────────────
        $admin = User::create([
            'name'     => 'Admin Daiman',
            'email'    => 'admin@demo.com',
            'phone'    => '+607-123 4567',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // ── Demo User ──────────────────────────
        $user = User::create([
            'name'     => 'Muhammad Dennish',
            'email'    => 'user@demo.com',
            'phone'    => '+601 2-345 6789',
            'password' => Hash::make('password'),
            'role'     => 'user',
        ]);

        // ── Extra Demo Users ───────────────────
        $users = [];
        $names = ['Ahmad Razif', 'Siti Aishah', 'Lee Wei Jian', 'Priya Rajan', 'Nurul Huda', 'Kevin Tan'];
        foreach ($names as $name) {
            $users[] = User::create([
                'name'     => $name,
                'email'    => strtolower(str_replace(' ', '.', $name)) . '@demo.com',
                'phone'    => '+601' . rand(0, 9) . '-' . rand(1000000, 9999999),
                'password' => Hash::make('password'),
                'role'     => 'user',
            ]);
        }

        // ── Courts ─────────────────────────────
        $courts = [
            ['name' => 'Court 1 — Champion Hall',  'type' => 'Indoor',  'price_per_hour' => 20.00, 'description' => 'Professional-grade indoor court with LED lighting and wooden flooring.'],
            ['name' => 'Court 2 — Olympic Wing',   'type' => 'Indoor',  'price_per_hour' => 20.00, 'description' => 'Regulation-size court with air conditioning and spectator seating.'],
            ['name' => 'Court 3 — Express Court',  'type' => 'Indoor',  'price_per_hour' => 18.00, 'description' => 'Compact indoor court, perfect for quick practice sessions.'],
            ['name' => 'Court 4 — Garden Court',   'type' => 'Outdoor', 'price_per_hour' => 15.00, 'description' => 'Open-air court surrounded by landscaped gardens. Available during dry weather.'],
            ['name' => 'Court 5 — Sunrise Court',  'type' => 'Outdoor', 'price_per_hour' => 15.00, 'description' => 'East-facing outdoor court ideal for morning sessions.'],
            ['name' => 'Court 6 — Pro Arena',      'type' => 'Indoor',  'price_per_hour' => 25.00, 'description' => 'Premium court with automated lighting and tournament-quality flooring.'],
            ['name' => 'Court 7 — Training Bay',   'type' => 'Indoor',  'price_per_hour' => 18.00, 'description' => 'Dedicated training court with ball machine docking station.'],
            ['name' => 'Court 8 — Community Hall', 'type' => 'Indoor',  'price_per_hour' => 16.00, 'description' => 'Budget-friendly court for recreational play.'],
        ];

        $courtModels = [];
        foreach ($courts as $court) {
            $courtModels[] = Court::create(array_merge($court, ['is_available' => true]));
        }

        // Block one court for maintenance
        $courtModels[6]->update(['is_available' => false]);

        // ── Sample Bookings ────────────────────
        $allUsers   = array_merge([$user], $users);
        $timeSlots  = [
            '07:00 – 08:00', '08:00 – 09:00', '09:00 – 10:00',
            '10:00 – 11:00', '11:00 – 12:00', '14:00 – 15:00',
            '15:00 – 16:00', '16:00 – 17:00', '17:00 – 18:00',
            '19:00 – 20:00', '20:00 – 21:00',
        ];

        $statuses = ['confirmed', 'confirmed', 'confirmed', 'pending', 'cancelled'];

        $usedSlots = []; // court_id + date + slot uniqueness

        for ($i = 0; $i < 40; $i++) {
            $randomUser   = $allUsers[array_rand($allUsers)];
            $randomCourt  = $courtModels[array_rand(array_slice($courtModels, 0, 6))]; // only available courts
            $daysOffset   = rand(-30, 14);
            $date         = now()->addDays($daysOffset)->format('Y-m-d');
            $slot         = $timeSlots[array_rand($timeSlots)];
            $key          = "{$randomCourt->id}_{$date}_{$slot}";

            if (isset($usedSlots[$key])) continue;
            $usedSlots[$key] = true;

            $status = $statuses[array_rand($statuses)];
            // Past bookings should be confirmed or completed
            if ($daysOffset < 0) $status = 'confirmed';

            Booking::create([
                'user_id'        => $randomUser->id,
                'court_id'       => $randomCourt->id,
                'booking_date'   => $date,
                'time_slot'      => $slot,
                'status'         => $status,
                'amount'         => $randomCourt->price_per_hour,
                'payment_method' => collect(['credit_card', 'fpx', 'ewallet'])->random(),
                'payment_status' => $status === 'confirmed' ? 'paid' : ($status === 'cancelled' ? 'failed' : 'unpaid'),
                'qr_token'       => strtoupper(Str::random(10)),
                'notes'          => rand(0, 3) === 0 ? 'Please prepare extra shuttlecocks.' : null,
                'qr_scanned_at'  => ($daysOffset < -1 && $status === 'confirmed') ? now()->subDays(rand(1, 20)) : null,
            ]);
        }

        $this->command->info('✅  Seeding complete!');
        $this->command->info('👤  Admin:  admin@demo.com / password');
        $this->command->info('👤  User:   user@demo.com  / password');
        $this->command->info('🏸  ' . count($courtModels) . ' courts and ~40 bookings created.');
    }
}
