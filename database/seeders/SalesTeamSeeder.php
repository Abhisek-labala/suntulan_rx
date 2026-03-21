<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Designation;
use App\Models\Zone;
use App\Models\Region;
use App\Models\Hq;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SalesTeamSeeder extends Seeder
{
    public function run(): void
    {
        $designation = Designation::where('name', 'Field Sales Manager')->first();
        if (!$designation) {
            $designation = Designation::create(['name' => 'Field Sales Manager']);
        }

        $salesTeam = [
            ['prefix' => 'Mr.', 'name' => 'Pranit Vishwas Indulkar', 'hq' => 'Kolhapur', 'region' => 'Pune', 'zone' => 'West'],
            ['prefix' => 'Mr.', 'name' => 'Tanmoy Paul', 'hq' => 'Howrah', 'region' => 'Kolkata', 'zone' => 'East'],
            ['prefix' => 'Mr.', 'name' => 'Nishant Kumar', 'hq' => 'Ghaziabad', 'region' => 'Lucknow', 'zone' => 'North'],
            ['prefix' => 'Mr.', 'name' => 'Nikhil Khaitan', 'hq' => 'Bengaluru', 'region' => 'Bengaluru', 'zone' => 'South 1'],
            ['prefix' => 'Mr.', 'name' => 'Rahul Tanaji Gaykwad', 'hq' => 'Hubballi', 'region' => 'Bengaluru', 'zone' => 'South 1'],
            ['prefix' => 'Mr.', 'name' => 'Ghanshyam Dangi', 'hq' => 'Indore', 'region' => 'Nagpur', 'zone' => 'West'],
            ['prefix' => 'Mr.', 'name' => 'Chander Arora', 'hq' => 'Chandigarh', 'region' => 'Chandigarh', 'zone' => 'North'],
            ['prefix' => 'Mr.', 'name' => 'D. Murali', 'hq' => 'Chennai', 'region' => 'Chennai', 'zone' => 'South 2'],
            ['prefix' => 'Mr.', 'name' => 'Rohan Narayana Shetty', 'hq' => 'Mangaluru', 'region' => 'Bengaluru', 'zone' => 'South 1'],
            ['prefix' => 'Mr.', 'name' => 'Amit Janardan Pandey', 'hq' => 'Lucknow', 'region' => 'Lucknow', 'zone' => 'North'],
            ['prefix' => 'Mr.', 'name' => 'Arnab Debnath', 'hq' => 'Kolkata', 'region' => 'Kolkata', 'zone' => 'East'],
            ['prefix' => 'Mr.', 'name' => 'Dinesh .', 'hq' => 'Chennai', 'region' => 'Chennai', 'zone' => 'South 2'],
            ['prefix' => 'Mr.', 'name' => 'Santhosh K', 'hq' => 'Chennai', 'region' => 'Chennai', 'zone' => 'South 2'],
            ['prefix' => 'Mr.', 'name' => 'Pritam Chaudhary', 'hq' => 'Mumbai', 'region' => 'Mumbai', 'zone' => 'West'],
            ['prefix' => 'Mr.', 'name' => 'Bineesh P K', 'hq' => 'Kozhikode', 'region' => 'Ernakulam', 'zone' => 'South 2'],
            ['prefix' => 'Mr.', 'name' => 'Bhanu Pratap Singh', 'hq' => 'Thiruvananthapuram', 'region' => 'Ernakulam', 'zone' => 'South 2'],
            ['prefix' => 'Mr.', 'name' => 'Boddupally Raja Rajeshwar Rao', 'hq' => 'Hyderabad', 'region' => 'Hyderabad', 'zone' => 'South 1'],
            ['prefix' => 'Mr.', 'name' => 'Partha Sen', 'hq' => 'Bardhaman', 'region' => 'Kolkata', 'zone' => 'East'],
            ['prefix' => 'Mr.', 'name' => 'Narender Kumar', 'hq' => 'Delhi', 'region' => 'Delhi', 'zone' => 'North'],
            ['prefix' => 'Mr.', 'name' => 'Aditya Rathi', 'hq' => 'Delhi', 'region' => 'Delhi', 'zone' => 'North'],
            ['prefix' => 'Mr.', 'name' => 'Chinmay Tewary', 'hq' => 'Bhubaneshwar', 'region' => 'Patna', 'zone' => 'East'],
            ['prefix' => 'Mr.', 'name' => 'Dheerajkumar Amarjeet Gupta', 'hq' => 'Thane', 'region' => 'Mumbai', 'zone' => 'West'],
            ['prefix' => 'Mr.', 'name' => 'Ram Bhajan Yadav', 'hq' => 'Varanasi', 'region' => 'Lucknow', 'zone' => 'North'],
            ['prefix' => 'Mr.', 'name' => 'Prajna Narendra', 'hq' => 'Patna', 'region' => 'Patna', 'zone' => 'East'],
            ['prefix' => 'Mr.', 'name' => 'Ruttala Sai Kumar', 'hq' => 'Visakhapatnam', 'region' => 'Hyderabad', 'zone' => 'South 1'],
            ['prefix' => 'Mr.', 'name' => 'Mittakola Shravan Kumar', 'hq' => 'Hyderabad', 'region' => 'Hyderabad', 'zone' => 'South 1'],
            ['prefix' => 'Mr.', 'name' => 'Kambham Pavan Kalyan', 'hq' => 'Guntur', 'region' => 'Hyderabad', 'zone' => 'South 1'],
            ['prefix' => 'Mr.', 'name' => 'Arun K R', 'hq' => 'Ernakulam', 'region' => 'Ernakulam', 'zone' => 'South 2'],
            ['prefix' => 'Mr.', 'name' => 'Shamshad Malik', 'hq' => 'Delhi', 'region' => 'Delhi', 'zone' => 'North'],
            ['prefix' => 'Mr.', 'name' => 'Chinnarasu S.', 'hq' => 'Salem', 'region' => 'Coimbatore', 'zone' => 'South 2'],
            ['prefix' => 'Mr.', 'name' => 'Joy Mukherjee', 'hq' => 'Kolkata', 'region' => 'Kolkata', 'zone' => 'East'],
            ['prefix' => 'Mr.', 'name' => 'Akshay Balchandra Bhiungade', 'hq' => 'Mumbai', 'region' => 'Mumbai', 'zone' => 'West'],
            ['prefix' => 'Mr.', 'name' => 'Subham Banik', 'hq' => 'Guwahati', 'region' => 'Kolkata', 'zone' => 'East'],
            ['prefix' => 'Mr.', 'name' => 'Pankaj Joshi', 'hq' => 'Meerut', 'region' => 'Lucknow', 'zone' => 'North'],
            ['prefix' => 'Mr.', 'name' => 'Pankaj Middha', 'hq' => 'Jodhpur', 'region' => 'Jaipur', 'zone' => 'North'],
            ['prefix' => 'Mr.', 'name' => 'Pandiri Nagesh', 'hq' => 'Hyderabad', 'region' => 'Hyderabad', 'zone' => 'South 1'],
            ['prefix' => 'Mr.', 'name' => 'R. Raja', 'hq' => 'Coimbatore', 'region' => 'Coimbatore', 'zone' => 'South 2'],
            ['prefix' => 'Mr.', 'name' => 'Ranjith Babu M. D.', 'hq' => 'Madurai', 'region' => 'Chennai', 'zone' => 'South 2'],
            ['prefix' => 'Mr.', 'name' => 'Swapnil Sudhakar Karanjkar', 'hq' => 'Pune', 'region' => 'Pune', 'zone' => 'West'],
            ['prefix' => 'Mr.', 'name' => 'Jitesh Naresh Rathi', 'hq' => 'Nagpur', 'region' => 'Nagpur', 'zone' => 'West'],
            ['prefix' => 'Mr.', 'name' => 'Kopuri Subhash', 'hq' => 'Vijayawada', 'region' => 'Hyderabad', 'zone' => 'South 1'],
            ['prefix' => 'Mr.', 'name' => 'Ranveer Kumar', 'hq' => 'Bengaluru', 'region' => 'Bengaluru', 'zone' => 'South 1'],
            ['prefix' => 'Mr.', 'name' => 'Lakhwinder Singh', 'hq' => 'Ludhiana', 'region' => 'Chandigarh', 'zone' => 'North'],
            ['prefix' => 'Mr.', 'name' => 'Shaikh Mohammed Husain Kapil', 'hq' => 'Mumbai', 'region' => 'Mumbai', 'zone' => 'West'],
            ['prefix' => 'Mr.', 'name' => 'Dhaval Chetankumar Patel', 'hq' => 'Vadodara', 'region' => 'Ahmedabad', 'zone' => 'West'],
            ['prefix' => 'Mr.', 'name' => 'Zala Hardik Harishbhai', 'hq' => 'Ahmedabad', 'region' => 'Ahmedabad', 'zone' => 'West'],
            ['prefix' => 'Mr.', 'name' => 'Ankit Sain', 'hq' => 'Jaipur', 'region' => 'Jaipur', 'zone' => 'North'],
        ];

        foreach ($salesTeam as $index => $data) {
            $zone = Zone::where('name', $data['zone'])->first();
            $region = Region::where('name', $data['region'])->where('zone_id', $zone?->id)->first();
            $hq = Hq::where('name', $data['hq'])->where('region_id', $region?->id)->first();

            $username = Str::slug($data['name'], '.');
            
            // Generate a unique employee ID if not present
            $employeeId = 'ST' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

            $password = Str::random(10);

            User::updateOrCreate(
                ['username' => $username],
                [
                    'name' => $data['name'],
                    'prefix' => $data['prefix'],
                    'password' => Hash::make($password),
                    'plain_password' => $password,
                    'role' => 'sales_team',
                    'employee_id' => $employeeId,
                    'designation_id' => $designation->id,
                    'zone_id' => $zone?->id,
                    'region_id' => $region?->id,
                    'hq_id' => $hq?->id,
                ]
            );
        }
    }
}
