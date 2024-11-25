<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use LaraSnap\LaravelAdmin\Models\User;
use LaraSnap\LaravelAdmin\Models\UserProfile;
use LaraSnap\LaravelAdmin\Models\Role;
use LaraSnap\LaravelAdmin\Models\Screen;
use LaraSnap\LaravelAdmin\Models\RoleScreen;
use LaraSnap\LaravelAdmin\Models\Module;
use LaraSnap\LaravelAdmin\Models\Menu;
use LaraSnap\LaravelAdmin\Models\MenuItem;

class DocumentManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Role Seed
        $role = Role::where('name', 'super-admin')->first();

        $module11 = new Module;
        $module11->label = 'Document Category';
        $module11->save();

        $module12 = new Module;
        $module12->label = 'Document Upload';
        $module12->save();

        $screens = [           
            ['name' => 'category.index', 'label' => 'Category List', 'module_id' => $module11->id],
            ['name' => 'category.create', 'label' => 'Category Creation', 'module_id' => $module11->id],
            ['name' => 'category.update', 'label' => 'Category Edit', 'module_id' => $module11->id],
            ['name' => 'category.delete', 'label' => 'Category Delete', 'module_id' => $module11->id],
            ['name' => 'document.list', 'label' => 'Document List', 'module_id' => $module12->id],
            ['name' => 'document.index', 'label' => 'Document Index', 'module_id' => $module12->id],
            ['name' => 'document.upload', 'label' => 'Document Upload', 'module_id' => $module12->id],
            ['name' => 'view.document', 'label' => 'Document View', 'module_id' => $module12->id],
            ['name' => 'download.document', 'label' => 'Document Download', 'module_id' => $module12->id],
            ['name' => 'delete.document', 'label' => 'Document Delete', 'module_id' => $module12->id],
        ];

        foreach ($screens as $screen) {
            $newScreen = Screen::create($screen);
            $role->assignScreen($newScreen->id);
        }

        //Menu Seed 
        $menu = Menu::where('name', 'admin')->first();
        
        // Menu Items
        $menuItems = [
            [
                'title' => "Document Category",
                'icon' => "fa-list",
                'order' => 10,
                'target' => "_self",
                'route' => "category.index",
            ],
            [
                'title' => "Document",
                'icon' => "fa-upload",
                'order' => 11,
                'target' => "_self",
                'route' => "document.list",
            ],
        ];
        
        // Save Menu Items
        foreach ($menuItems as $menuItemData) {
            $menuItem = new MenuItem($menuItemData);
            $menu->items()->save($menuItem); // Assuming items() is the correct relationship
        }
    }
}