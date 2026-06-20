<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Restaurant;
use App\Models\Branch;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\DiningArea;
use App\Models\Table;
use App\Models\Setting;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\ItemVariant;
use App\Models\ItemAddon;
use App\Models\InventoryItem;
use App\Models\Recipe;
use App\Models\ExpenseCategory;
use App\Models\Supplier;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Restaurant
        $restaurant = Restaurant::create([
            'name' => 'Gourmet Hub',
            'logo_path' => null,
            'address' => '123 Foodie Avenue, Culinary City',
            'phone' => '+1 (555) 123-4567',
            'currency' => 'USD',
            'tax_settings' => ['vat_percentage' => 15, 'service_charge_percentage' => 5],
            'working_hours' => ['open' => '08:00', 'close' => '23:00'],
        ]);

        // 2. Create Branch
        $branch = Branch::create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Gourmet Hub Downtown',
            'address' => '456 Main Street, Downtown District',
            'phone' => '+1 (555) 765-4321',
            'is_main' => true,
        ]);

        // 3. Create Settings
        $settings = [
            'sla_waiter_approval_minutes' => '2',
            'sla_kitchen_preparation_minutes' => '15',
            'sla_waiter_pickup_minutes' => '2',
            'sla_delivery_minutes' => '3',
            'inventory_deduction_trigger' => 'on_served', // on_approval, on_served, on_paid
        ];
        foreach ($settings as $key => $val) {
            Setting::create([
                'branch_id' => $branch->id,
                'key' => $key,
                'value' => $val,
            ]);
        }

        // 4. Create Permissions
        $permissionsList = [
            ['name' => 'View Dashboard', 'slug' => 'view-dashboard', 'module' => 'dashboard'],
            ['name' => 'Manage Menu', 'slug' => 'manage-menu', 'module' => 'menu'],
            ['name' => 'View Menu', 'slug' => 'view-menu', 'module' => 'menu'],
            ['name' => 'Approve QR Orders', 'slug' => 'approve-orders', 'module' => 'orders'],
            ['name' => 'Create POS Orders', 'slug' => 'create-orders', 'module' => 'orders'],
            ['name' => 'View Kitchen display', 'slug' => 'view-kitchen', 'module' => 'kitchen'],
            ['name' => 'Update Kitchen status', 'slug' => 'update-kitchen', 'module' => 'kitchen'],
            ['name' => 'Manage billing and POS payments', 'slug' => 'manage-billing', 'module' => 'pos'],
            ['name' => 'Manage Inventory', 'slug' => 'manage-inventory', 'module' => 'inventory'],
            ['name' => 'Manage Expenses', 'slug' => 'manage-expenses', 'module' => 'expenses'],
            ['name' => 'Approve Expenses', 'slug' => 'approve-expenses', 'module' => 'expenses'],
            ['name' => 'View Reports', 'slug' => 'view-reports', 'module' => 'reports'],
            ['name' => 'Manage Users and Roles', 'slug' => 'manage-users', 'module' => 'users'],
        ];

        $permissions = [];
        foreach ($permissionsList as $p) {
            $permissions[$p['slug']] = Permission::create($p);
        }

        // 5. Create Roles
        $rolesList = [
            ['name' => 'Super Admin', 'slug' => 'super-admin', 'description' => 'Global System administrator'],
            ['name' => 'Restaurant Owner', 'slug' => 'restaurant-owner', 'description' => 'Restaurant owner with full access'],
            ['name' => 'Manager', 'slug' => 'manager', 'description' => 'Branch manager supervising operations'],
            ['name' => 'Waiter', 'slug' => 'waiter', 'description' => 'Service staff approving QR orders and serving tables'],
            ['name' => 'Chef', 'slug' => 'chef', 'description' => 'Kitchen staff preparing items'],
            ['name' => 'Cashier', 'slug' => 'cashier', 'description' => 'Checkout and payment collector'],
            ['name' => 'Inventory Manager', 'slug' => 'inventory-manager', 'description' => 'Store manager controlling ingredients'],
            ['name' => 'Accountant', 'slug' => 'accountant', 'description' => 'Finance staff entering expenses and reviewing logs'],
        ];

        $roles = [];
        foreach ($rolesList as $r) {
            $r['branch_id'] = $branch->id;
            $roles[$r['slug']] = Role::create($r);
        }

        // 6. Map Permissions to Roles
        // Owner has everything
        $roles['restaurant-owner']->permissions()->sync(Permission::all());
        $roles['super-admin']->permissions()->sync(Permission::all());

        // Manager Permissions
        $roles['manager']->permissions()->sync([
            $permissions['view-dashboard']->id,
            $permissions['view-menu']->id,
            $permissions['approve-orders']->id,
            $permissions['create-orders']->id,
            $permissions['view-kitchen']->id,
            $permissions['update-kitchen']->id,
            $permissions['manage-billing']->id,
            $permissions['manage-inventory']->id,
            $permissions['manage-expenses']->id,
            $permissions['approve-expenses']->id,
            $permissions['view-reports']->id,
        ]);

        // Waiter Permissions
        $roles['waiter']->permissions()->sync([
            $permissions['view-dashboard']->id,
            $permissions['view-menu']->id,
            $permissions['approve-orders']->id,
            $permissions['create-orders']->id,
            $permissions['view-kitchen']->id,
        ]);

        // Chef Permissions
        $roles['chef']->permissions()->sync([
            $permissions['view-kitchen']->id,
            $permissions['update-kitchen']->id,
            $permissions['view-menu']->id,
        ]);

        // Cashier Permissions
        $roles['cashier']->permissions()->sync([
            $permissions['view-dashboard']->id,
            $permissions['view-menu']->id,
            $permissions['create-orders']->id,
            $permissions['manage-billing']->id,
        ]);

        // Inventory Manager Permissions
        $roles['inventory-manager']->permissions()->sync([
            $permissions['view-dashboard']->id,
            $permissions['view-menu']->id,
            $permissions['manage-inventory']->id,
        ]);

        // Accountant Permissions
        $roles['accountant']->permissions()->sync([
            $permissions['view-dashboard']->id,
            $permissions['manage-expenses']->id,
            $permissions['view-reports']->id,
        ]);

        // 7. Create Staff Users
        $defaultPassword = Hash::make('password');
        
        $usersList = [
            ['name' => 'Alice Owner', 'email' => 'owner@gourmethub.com', 'role_slug' => 'restaurant-owner'],
            ['name' => 'Bob Manager', 'email' => 'manager@gourmethub.com', 'role_slug' => 'manager'],
            ['name' => 'Charlie Waiter', 'email' => 'waiter@gourmethub.com', 'role_slug' => 'waiter'],
            ['name' => 'David Chef', 'email' => 'chef@gourmethub.com', 'role_slug' => 'chef'],
            ['name' => 'Emma Cashier', 'email' => 'cashier@gourmethub.com', 'role_slug' => 'cashier'],
            ['name' => 'Fred Storekeeper', 'email' => 'inventory@gourmethub.com', 'role_slug' => 'inventory-manager'],
            ['name' => 'Grace Accountant', 'email' => 'accountant@gourmethub.com', 'role_slug' => 'accountant'],
        ];

        foreach ($usersList as $u) {
            User::create([
                'branch_id' => $branch->id,
                'role_id' => $roles[$u['role_slug']]->id,
                'name' => $u['name'],
                'email' => $u['email'],
                'password' => $defaultPassword,
                'phone' => '+1 (555) ' . rand(100, 999) . '-' . rand(1000, 9999),
                'status' => 'active',
            ]);
        }

        // 8. Create Dining Areas
        $groundArea = DiningArea::create([
            'branch_id' => $branch->id,
            'name' => 'Ground Floor',
            'floor' => 1,
            'status' => 'active',
        ]);

        $rooftopArea = DiningArea::create([
            'branch_id' => $branch->id,
            'name' => 'Rooftop',
            'floor' => 2,
            'status' => 'active',
        ]);

        // 9. Create Tables
        for ($i = 1; $i <= 5; $i++) {
            Table::create([
                'area_id' => $groundArea->id,
                'table_number' => "Table $i",
                'qr_code' => "GHDT_GF_T$i",
                'qr_url' => url("/menu?table=Table+$i&area=" . $groundArea->id),
                'capacity' => 4,
                'status' => 'available',
            ]);
        }

        for ($i = 11; $i <= 13; $i++) {
            Table::create([
                'area_id' => $rooftopArea->id,
                'table_number' => "Table $i",
                'qr_code' => "GHDT_RT_T$i",
                'qr_url' => url("/menu?table=Table+$i&area=" . $rooftopArea->id),
                'capacity' => 6,
                'status' => 'available',
            ]);
        }

        // 10. Create Menu Categories
        $categoriesList = ['Appetizers', 'Mains', 'Desserts', 'Drinks'];
        $categories = [];
        foreach ($categoriesList as $index => $catName) {
            $categories[$catName] = MenuCategory::create([
                'branch_id' => $branch->id,
                'name' => $catName,
                'sort_order' => $index,
                'status' => 'active',
            ]);
        }

        // 11. Create Menu Items
        $items = [
            // Appetizers
            [
                'category_id' => $categories['Appetizers']->id,
                'name' => 'Garlic Bread',
                'description' => 'Toasted baguette slices with fresh garlic butter, parsley, and olive oil.',
                'price' => 5.50,
                'estimated_prep_time' => 8,
                'availability_status' => 'available',
            ],
            [
                'category_id' => $categories['Appetizers']->id,
                'name' => 'Bruschetta',
                'description' => 'Grilled bread rubbed with garlic and topped with diced tomatoes, olive oil, basil, and salt.',
                'price' => 6.50,
                'estimated_prep_time' => 10,
                'availability_status' => 'available',
            ],
            // Mains
            [
                'category_id' => $categories['Mains']->id,
                'name' => 'Classic Beef Burger',
                'description' => 'Succulent beef patty served in a toasted bun with lettuce, tomatoes, and custom burger sauce.',
                'price' => 12.50,
                'estimated_prep_time' => 12,
                'availability_status' => 'available',
            ],
            [
                'category_id' => $categories['Mains']->id,
                'name' => 'Margherita Pizza',
                'description' => 'Classic sourdough pizza topped with fresh tomato sauce, mozzarella cheese, and fresh basil leaves.',
                'price' => 11.00,
                'estimated_prep_time' => 15,
                'availability_status' => 'available',
            ],
            [
                'category_id' => $categories['Mains']->id,
                'name' => 'Pasta Carbonara',
                'description' => 'Creamy egg yolk and pecorino romano sauce tossed with fresh pasta and crispy pancetta.',
                'price' => 14.00,
                'estimated_prep_time' => 14,
                'availability_status' => 'available',
            ],
            // Desserts
            [
                'category_id' => $categories['Desserts']->id,
                'name' => 'Tiramisu',
                'description' => 'Classic Italian coffee-flavoured dessert made of ladyfingers dipped in coffee, layered with whipped mascarpone.',
                'price' => 7.00,
                'estimated_prep_time' => 5,
                'availability_status' => 'available',
            ],
            [
                'category_id' => $categories['Desserts']->id,
                'name' => 'Chocolate Fudge Cake',
                'description' => 'Rich, warm chocolate cake with melting fudge frosting, served with vanilla ice cream.',
                'price' => 6.50,
                'estimated_prep_time' => 5,
                'availability_status' => 'available',
            ],
            // Drinks
            [
                'category_id' => $categories['Drinks']->id,
                'name' => 'Fresh Orange Juice',
                'description' => 'Freshly squeezed sweet oranges, served chilled.',
                'price' => 4.00,
                'estimated_prep_time' => 3,
                'availability_status' => 'available',
            ],
            [
                'category_id' => $categories['Drinks']->id,
                'name' => 'Espresso',
                'description' => 'Strong, rich espresso shot made of selected Arabica coffee beans.',
                'price' => 3.00,
                'estimated_prep_time' => 3,
                'availability_status' => 'available',
            ]
        ];

        $menuItems = [];
        foreach ($items as $item) {
            $menuItems[$item['name']] = MenuItem::create($item);
        }

        // 12. Create Variants
        $burgerVariant1 = ItemVariant::create([
            'menu_item_id' => $menuItems['Classic Beef Burger']->id,
            'name' => 'Regular',
            'price_delta' => 0.00,
        ]);

        $burgerVariant2 = ItemVariant::create([
            'menu_item_id' => $menuItems['Classic Beef Burger']->id,
            'name' => 'Double Patty',
            'price_delta' => 3.50,
        ]);

        // 13. Create Add-ons
        $addonsList = [
            ['name' => 'Extra Cheese', 'price' => 1.50],
            ['name' => 'Bacon', 'price' => 2.00],
            ['name' => 'Guacamole', 'price' => 1.80],
        ];

        $addons = [];
        foreach ($addonsList as $addon) {
            $addon['branch_id'] = $branch->id;
            $addons[$addon['name']] = ItemAddon::create($addon);
        }

        // Connect Add-ons to Menu Items
        $menuItems['Classic Beef Burger']->addons()->sync([
            $addons['Extra Cheese']->id,
            $addons['Bacon']->id,
            $addons['Guacamole']->id,
        ]);
        $menuItems['Margherita Pizza']->addons()->sync([
            $addons['Extra Cheese']->id,
            $addons['Bacon']->id,
        ]);

        // 14. Create Inventory Items
        $inventoryList = [
            ['name' => 'Beef Patty', 'category' => 'Meat', 'unit' => 'pcs', 'current_stock' => 50, 'min_stock' => 10, 'reorder_level' => 15, 'average_cost' => 2.50],
            ['name' => 'Burger Bun', 'category' => 'Bakery', 'unit' => 'pcs', 'current_stock' => 60, 'min_stock' => 15, 'reorder_level' => 20, 'average_cost' => 0.50],
            ['name' => 'Cheddar Cheese Slice', 'category' => 'Dairy', 'unit' => 'pcs', 'current_stock' => 200, 'min_stock' => 50, 'reorder_level' => 70, 'average_cost' => 0.20],
            ['name' => 'Pizza Dough', 'category' => 'Bakery', 'unit' => 'pcs', 'current_stock' => 30, 'min_stock' => 10, 'reorder_level' => 12, 'average_cost' => 1.00],
            ['name' => 'Mozzarella Cheese', 'category' => 'Dairy', 'unit' => 'g', 'current_stock' => 5000, 'min_stock' => 1000, 'reorder_level' => 1500, 'average_cost' => 0.01],
            ['name' => 'Pasta Semolina', 'category' => 'Dry Goods', 'unit' => 'g', 'current_stock' => 4000, 'min_stock' => 1000, 'reorder_level' => 1500, 'average_cost' => 0.002],
            ['name' => 'Bacon Slices', 'category' => 'Meat', 'unit' => 'pcs', 'current_stock' => 150, 'min_stock' => 30, 'reorder_level' => 45, 'average_cost' => 0.40],
        ];

        $inventory = [];
        foreach ($inventoryList as $invItem) {
            $invItem['branch_id'] = $branch->id;
            $invItem['status'] = 'active';
            $inventory[$invItem['name']] = InventoryItem::create($invItem);
        }

        // 15. Create Recipes (Menu Item to Inventory Ingredient mapping)
        // Burger (Regular) -> 1 bun, 1 patty, 1 cheese slice
        Recipe::create([
            'menu_item_id' => $menuItems['Classic Beef Burger']->id,
            'item_variant_id' => $burgerVariant1->id,
            'inventory_item_id' => $inventory['Burger Bun']->id,
            'quantity_required' => 1,
            'unit' => 'pcs',
        ]);
        Recipe::create([
            'menu_item_id' => $menuItems['Classic Beef Burger']->id,
            'item_variant_id' => $burgerVariant1->id,
            'inventory_item_id' => $inventory['Beef Patty']->id,
            'quantity_required' => 1,
            'unit' => 'pcs',
        ]);
        Recipe::create([
            'menu_item_id' => $menuItems['Classic Beef Burger']->id,
            'item_variant_id' => $burgerVariant1->id,
            'inventory_item_id' => $inventory['Cheddar Cheese Slice']->id,
            'quantity_required' => 1,
            'unit' => 'pcs',
        ]);

        // Burger (Double Patty) -> 1 bun, 2 patties, 2 cheese slices
        Recipe::create([
            'menu_item_id' => $menuItems['Classic Beef Burger']->id,
            'item_variant_id' => $burgerVariant2->id,
            'inventory_item_id' => $inventory['Burger Bun']->id,
            'quantity_required' => 1,
            'unit' => 'pcs',
        ]);
        Recipe::create([
            'menu_item_id' => $menuItems['Classic Beef Burger']->id,
            'item_variant_id' => $burgerVariant2->id,
            'inventory_item_id' => $inventory['Beef Patty']->id,
            'quantity_required' => 2,
            'unit' => 'pcs',
        ]);
        Recipe::create([
            'menu_item_id' => $menuItems['Classic Beef Burger']->id,
            'item_variant_id' => $burgerVariant2->id,
            'inventory_item_id' => $inventory['Cheddar Cheese Slice']->id,
            'quantity_required' => 2,
            'unit' => 'pcs',
        ]);

        // Margherita Pizza -> 1 pizza dough, 150g Mozzarella Cheese
        Recipe::create([
            'menu_item_id' => $menuItems['Margherita Pizza']->id,
            'item_variant_id' => null,
            'inventory_item_id' => $inventory['Pizza Dough']->id,
            'quantity_required' => 1,
            'unit' => 'pcs',
        ]);
        Recipe::create([
            'menu_item_id' => $menuItems['Margherita Pizza']->id,
            'item_variant_id' => null,
            'inventory_item_id' => $inventory['Mozzarella Cheese']->id,
            'quantity_required' => 150,
            'unit' => 'g',
        ]);

        // Pasta Carbonara -> 100g Pasta, 3 Bacon Slices
        Recipe::create([
            'menu_item_id' => $menuItems['Pasta Carbonara']->id,
            'item_variant_id' => null,
            'inventory_item_id' => $inventory['Pasta Semolina']->id,
            'quantity_required' => 100,
            'unit' => 'g',
        ]);
        Recipe::create([
            'menu_item_id' => $menuItems['Pasta Carbonara']->id,
            'item_variant_id' => null,
            'inventory_item_id' => $inventory['Bacon Slices']->id,
            'quantity_required' => 3,
            'unit' => 'pcs',
        ]);

        // 16. Create Expense Categories
        $expCategories = ['Rent', 'Utilities', 'Salaries', 'Supplies', 'Marketing'];
        foreach ($expCategories as $expName) {
            ExpenseCategory::create([
                'branch_id' => $branch->id,
                'name' => $expName,
            ]);
        }

        // 17. Create Suppliers
        Supplier::create([
            'branch_id' => $branch->id,
            'name' => 'Metro Wholesale',
            'phone' => '+1 (555) 321-0987',
            'email' => 'metro@wholesale.com',
            'address' => '789 Warehouse Lane, Trade Zone',
            'status' => 'active',
        ]);

        Supplier::create([
            'branch_id' => $branch->id,
            'name' => 'Farm Fresh Produce',
            'phone' => '+1 (555) 987-6543',
            'email' => 'delivery@farmfresh.com',
            'address' => '101 Valley Farm Rd, Countryside',
            'status' => 'active',
        ]);
    }
}
