<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Office;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin users
        $superAdmin = User::create([
            'name' => 'Harold Peter',
            'email' => 'haroldpeter6@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => 'super_admin',
        ]);

        $admin = User::create([
            'name' => 'OfficeBnB Admin',
            'email' => 'admin@officebnb.test',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create office owners
        $owner1 = User::create([
            'name' => 'Sarah Nakato',
            'email' => 'sarah.nakato@officebnb.ug',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => 'owner',
        ]);

        $owner2 = User::create([
            'name' => 'James Okello',
            'email' => 'james.okello@officebnb.ug',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => 'owner',
        ]);

        $owner3 = User::create([
            'name' => 'Grace Nambooze',
            'email' => 'grace.nambooze@officebnb.ug',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => 'owner',
        ]);

        // Create renters
        $renter1 = User::create([
            'name' => 'David Musoke',
            'email' => 'david.musoke@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => 'renter',
        ]);

        $renter2 = User::create([
            'name' => 'Patricia Birungi',
            'email' => 'patricia.birungi@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => 'renter',
        ]);

        $renter3 = User::create([
            'name' => 'Michael Ssemakula',
            'email' => 'michael.ssemakula@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => 'renter',
        ]);

        // Create offices in Kampala
        $office1 = Office::create([
            'owner_id' => $owner1->id,
            'title' => 'Modern Executive Suite in Nakasero',
            'slug' => 'modern-executive-suite-nakasero-' . rand(1000, 9999),
            'city' => 'Kampala',
            'country' => 'Uganda',
            'address' => 'Plot 12, Nakasero Road, Kampala',
            'description' => 'A premium executive office suite located in the heart of Kampala business district. Features floor-to-ceiling windows with stunning city views, modern furniture, and all essential amenities for productive work.',
            'amenities' => ['High-speed WiFi', 'Meeting rooms', '24/7 access', 'Video conferencing', 'Kitchenette', 'Parking'],
            'image_urls' => [
                'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1200&q=80',
            ],
            'workspace_type' => 'Private office',
            'size_sqft' => 800,
            'meeting_rooms' => 2,
            'desks' => 8,
            'timezone' => 'Africa/Kampala',
            'capacity' => 10,
            'daily_rate' => 35000,
            'is_active' => true,
        ]);

        $office2 = Office::create([
            'owner_id' => $owner1->id,
            'title' => 'Creative Hub in Kololo',
            'slug' => 'creative-hub-kololo-' . rand(1000, 9999),
            'city' => 'Kampala',
            'country' => 'Uganda',
            'address' => '45 Kololo Hill Drive, Kampala',
            'description' => 'Bright and inspiring coworking space perfect for creative teams and startups. Located in the serene neighborhood of Kololo with lush green surroundings and peaceful work environment.',
            'amenities' => ['High-speed WiFi', 'Whiteboards', 'Phone booths', 'Coffee bar', 'Outdoor terrace', 'Printing'],
            'image_urls' => [
                'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1200&q=80',
            ],
            'workspace_type' => 'Coworking suite',
            'size_sqft' => 1200,
            'meeting_rooms' => 1,
            'desks' => 15,
            'timezone' => 'Africa/Kampala',
            'capacity' => 18,
            'daily_rate' => 28000,
            'is_active' => true,
        ]);

        $office3 = Office::create([
            'owner_id' => $owner2->id,
            'title' => 'Tech Innovation Space in Bugolobi',
            'slug' => 'tech-innovation-space-bugolobi-' . rand(1000, 9999),
            'city' => 'Kampala',
            'country' => 'Uganda',
            'address' => 'Plot 7, Spring Road, Bugolobi, Kampala',
            'description' => 'State-of-the-art office space designed for tech companies and innovation teams. Equipped with high-speed fiber internet, multiple meeting rooms, and modern collaborative spaces.',
            'amenities' => ['High-speed WiFi', 'Meeting rooms', 'Video conferencing', '24/7 access', 'Generator backup', 'Kitchen'],
            'image_urls' => [
                'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=1200&q=80',
            ],
            'workspace_type' => 'Private office',
            'size_sqft' => 1500,
            'meeting_rooms' => 3,
            'desks' => 20,
            'timezone' => 'Africa/Kampala',
            'capacity' => 25,
            'daily_rate' => 45000,
            'is_active' => true,
        ]);

        $office4 = Office::create([
            'owner_id' => $owner2->id,
            'title' => 'Professional Office in Ntinda',
            'slug' => 'professional-office-ntinda-' . rand(1000, 9999),
            'city' => 'Kampala',
            'country' => 'Uganda',
            'address' => 'Ntinda Shopping Center Complex, Kampala',
            'description' => 'Well-appointed professional office space in the bustling Ntinda business hub. Ideal for consulting firms, NGOs, and small businesses looking for a prime location with excellent accessibility.',
            'amenities' => ['High-speed WiFi', 'Meeting room', 'Reception area', 'Parking', 'Security', 'Cleaning service'],
            'image_urls' => [
                'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1200&q=80',
            ],
            'workspace_type' => 'Private office',
            'size_sqft' => 600,
            'meeting_rooms' => 1,
            'desks' => 6,
            'timezone' => 'Africa/Kampala',
            'capacity' => 8,
            'daily_rate' => 25000,
            'is_active' => true,
        ]);

        $office5 = Office::create([
            'owner_id' => $owner3->id,
            'title' => 'Garden Office in Muyenga',
            'slug' => 'garden-office-muyenga-' . rand(1000, 9999),
            'city' => 'Kampala',
            'country' => 'Uganda',
            'address' => 'Tank Hill Road, Muyenga, Kampala',
            'description' => 'Unique office space with beautiful garden views, perfect for teams seeking a calm and inspiring work environment away from city noise. Features natural lighting and outdoor meeting areas.',
            'amenities' => ['High-speed WiFi', 'Garden access', 'Outdoor seating', 'Parking', 'Kitchenette', 'Natural lighting'],
            'image_urls' => [
                'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1200&q=80',
            ],
            'workspace_type' => 'Coworking suite',
            'size_sqft' => 900,
            'meeting_rooms' => 2,
            'desks' => 12,
            'timezone' => 'Africa/Kampala',
            'capacity' => 15,
            'daily_rate' => 32000,
            'is_active' => true,
        ]);

        // Create offices in Entebbe
        $office6 = Office::create([
            'owner_id' => $owner3->id,
            'title' => 'Lakeside Business Center in Entebbe',
            'slug' => 'lakeside-business-center-entebbe-' . rand(1000, 9999),
            'city' => 'Entebbe',
            'country' => 'Uganda',
            'address' => 'Church Road, Entebbe',
            'description' => 'Premium office space near Lake Victoria, offering a tranquil work environment with easy access to Entebbe International Airport. Perfect for international teams and businesses requiring frequent travel.',
            'amenities' => ['High-speed WiFi', 'Meeting rooms', 'Airport shuttle', 'Lake view', 'Café', '24/7 access'],
            'image_urls' => [
                'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=1200&q=80',
            ],
            'workspace_type' => 'Private office',
            'size_sqft' => 1000,
            'meeting_rooms' => 2,
            'desks' => 10,
            'timezone' => 'Africa/Kampala',
            'capacity' => 12,
            'daily_rate' => 38000,
            'is_active' => true,
        ]);

        $office7 = Office::create([
            'owner_id' => $admin->id,
            'title' => 'Boutique Office in Acacia Avenue',
            'slug' => 'boutique-office-acacia-avenue-' . rand(1000, 9999),
            'city' => 'Kampala',
            'country' => 'Uganda',
            'address' => 'Acacia Avenue, Kololo, Kampala',
            'description' => 'Elegant boutique office space in one of Kampala\'s most prestigious addresses. Features modern design, quiet work zones, and premium facilities for discerning professionals.',
            'amenities' => ['High-speed WiFi', 'Video conferencing', 'Private offices', 'Lounge area', 'Security', 'Parking'],
            'image_urls' => [
                'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1200&q=80',
            ],
            'workspace_type' => 'Private office',
            'size_sqft' => 700,
            'meeting_rooms' => 1,
            'desks' => 8,
            'timezone' => 'Africa/Kampala',
            'capacity' => 10,
            'daily_rate' => 40000,
            'is_active' => true,
        ]);

        $office8 = Office::create([
            'owner_id' => $superAdmin->id,
            'title' => 'Smart Office in Lugogo',
            'slug' => 'smart-office-lugogo-' . rand(1000, 9999),
            'city' => 'Kampala',
            'country' => 'Uganda',
            'address' => 'Lugogo Bypass, Kampala',
            'description' => 'Contemporary smart office with advanced technology integration and flexible workspace configurations. Located near Lugogo Mall for convenient access to amenities and services.',
            'amenities' => ['High-speed WiFi', 'Smart screens', 'Meeting pods', 'Break room', 'Printing', 'Storage lockers'],
            'image_urls' => [
                'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=1200&q=80',
            ],
            'workspace_type' => 'Coworking suite',
            'size_sqft' => 1100,
            'meeting_rooms' => 2,
            'desks' => 14,
            'timezone' => 'Africa/Kampala',
            'capacity' => 16,
            'daily_rate' => 30000,
            'is_active' => true,
        ]);

        // Create some sample bookings
        $booking1 = $renter1->bookings()->create([
            'office_id' => $office1->id,
            'start_date' => now()->addDays(7),
            'end_date' => now()->addDays(10),
            'guests' => 5,
            'total_amount' => 105000,
            'currency' => 'UGX',
            'status' => 'approved',
            'notes' => 'Planning team workshop. Need projector and whiteboard.',
        ]);

        $booking1->payment()->create([
            'amount' => 105000,
            'currency' => 'UGX',
            'status' => 'paid',
            'provider' => 'manual',
            'reference' => 'PAY-' . strtoupper(substr(md5(uniqid()), 0, 10)),
            'paid_at' => now()->subDays(2),
        ]);

        $booking2 = $renter2->bookings()->create([
            'office_id' => $office3->id,
            'start_date' => now()->addDays(14),
            'end_date' => now()->addDays(21),
            'guests' => 8,
            'total_amount' => 315000,
            'currency' => 'UGX',
            'status' => 'pending',
            'notes' => 'Development sprint for mobile app project.',
        ]);

        $booking3 = $renter3->bookings()->create([
            'office_id' => $office5->id,
            'start_date' => now()->addDays(3),
            'end_date' => now()->addDays(5),
            'guests' => 4,
            'total_amount' => 64000,
            'currency' => 'UGX',
            'status' => 'approved',
            'notes' => 'Client meetings and presentations.',
        ]);

        $booking3->payment()->create([
            'amount' => 64000,
            'currency' => 'UGX',
            'status' => 'paid',
            'provider' => 'manual',
            'reference' => 'PAY-' . strtoupper(substr(md5(uniqid()), 0, 10)),
            'paid_at' => now()->subHours(12),
        ]);
    }
}
