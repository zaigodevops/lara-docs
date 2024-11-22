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
        //User Seed 
        $user = User::where('email', 'admin@admin.com')->first();
        if (!$user) {
            $user = new User;
            $user->email = 'admin@admin.com';
            $user->password = bcrypt('password');
            $user->status = 1;
            $user->created_by = 0;
            $user->save();

            $userProfile = new UserProfile;
            $userProfile->first_name = 'Super';
            $userProfile->last_name = 'Admin';
            $userProfile->mobile_no = 9876543210;
            $userProfile->address = 'Test Address';
            $userProfile->state = 'Test State';
            $userProfile->city = 'Test State';
            $userProfile->pincode = 98765;
            $user->userProfile()->save($userProfile);
        }

        //Role Seed
        $role = Role::where('name', 'super-admin')->first();
        if (!$role) {
            $role = new Role;
            $role->name = 'super-admin';
            $role->label = 'Super Admin';
            $role->save();
        }

        //User Role Mapping Seed
        $user->roles()->detach();
        $user->assignRole($role->id);

        //Module
        Module::whereIn('label', ['Document Category', 'Document Upload'])->delete();

        $module11 = new Module;
        $module11->label = 'Document Category';
        $module11->save();

        $module12 = new Module;
        $module12->label = 'Document Upload';
        $module12->save();

        $module13 = new Module;
        $module13->label = 'User Document List';
        $module13->save();

        //Screen Seed & Role Screen Mapping Seed
        Screen::whereIn('name', ['category.index', 'category.create', 'category.update', 'category.delete', 'document.list', 'document.index', 'document.upload', 'view.document', 'download.document', 'delete.document', 'user.document', 'user.view.document', 'user.download.document'])->delete();

        RoleScreen::where('role_id', $role->id)->delete();

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
            ['name' => 'user.document', 'label' => 'User Document List', 'module_id' => $module13->id],
            ['name' => 'user.view.document', 'label' => 'User Document View', 'module_id' => $module13->id],
            ['name' => 'user.download.document', 'label' => 'User Document Download', 'module_id' => $module13->id],
        ];

        foreach ($screens as $screen) {
            $newScreen = Screen::create($screen);
            $role->assignScreen($newScreen->id);
        }

        //Menu Seed 
        $menu = Menu::where('name', 'admin')->first();
        if (!$menu) {
            $menu = new Menu;
            $menu->name = 'admin';
            $menu->label = 'Admin';
            $menu->save();

            $menuItem10 = new MenuItem;
            $menuItem10->title = "Document Category";
            $menuItem10->icon = "fa-list";
            $menuItem10->order = 10;
            $menuItem10->target = "_self";
            $menuItem10->route = "category.index";

            $menuItem11 = new MenuItem;
            $menuItem11->title = "Document";
            $menuItem11->icon = "fa-upload";
            $menuItem11->order = 11;
            $menuItem11->target = "_self";
            $menuItem11->route = "document.list";

            $menuItem12 = new MenuItem;
            $menuItem12->title = "Document List";
            $menuItem12->icon = "fa-file-text";
            $menuItem12->order = 12;
            $menuItem12->target = "_self";
            $menuItem12->route = "user.document";

            $menu = $menu->items()->saveMany([$menuItem10,$menuItem11,$menuItem12]);
        }
    }
}