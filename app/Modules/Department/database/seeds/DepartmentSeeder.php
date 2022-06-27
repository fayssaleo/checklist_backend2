<?php

namespace App\Modules\Department\Database\Seeds;

use App\Modules\Department\Models\Department;
use App\Modules\Fonction\Models\Fonction;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $department = new Department();
        $department->name="IT";
        $department->save();
        //-----------------------------

        $fonction= new Fonction();
        $fonction->name="Technicien";
        $fonction->department_id=$department->id;
        $fonction->save();
        //-----------------------------

        $fonction= new Fonction();
        $fonction->name="Supervisor";
        $fonction->department_id=$department->id;
        $fonction->save();
        //-----------------------------



        $department = new Department();
        $department->name="Technique";
        $department->save();
        //-----------------------------
        $fonction= new Fonction();
        $fonction->name="Technicien";
        $fonction->department_id=$department->id;
        $fonction->save();
        //-----------------------------

        $fonction= new Fonction();
        $fonction->name="Supervisor";
        $fonction->department_id=$department->id;
        $fonction->save();
        //-----------------------------




        $department = new Department();
        $department->name="Operations";
        $department->save();
        //-----------------------------
        $fonction= new Fonction();
        $fonction->name="Driver";
        $fonction->department_id=$department->id;
        $fonction->save();
        //-----------------------------

        $fonction= new Fonction();
        $fonction->name="Foreman";
        $fonction->department_id=$department->id;
        $fonction->save();
        //-----------------------------
        $fonction= new Fonction();
        $fonction->name="Shift Manager";
        $fonction->department_id=$department->id;
        $fonction->save();
        //-----------------------------





    }
}

