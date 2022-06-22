<?php

namespace App\Modules\Equipment\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Equipment\Models\Equipment;
use App\Modules\ProfileGroup\Models\ProfileGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EquipmentController extends Controller
{

    public function index(){

        $equipment=Equipment::with('profileGroup.department')->get();

        return [
            "payload" => $equipment,
            "status" => "200_00"
        ];
    }

    public function get($id){
        $equipment=Equipment::find($id);
        if(!$equipment){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }
        else {
            $equipment->profileGroup=$equipment->profileGroup;
            $equipment->profileGroup->department=$equipment->profileGroup->department;
            return [
                "payload" => $equipment,
                "status" => "200_1"
            ];
        }
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            "name" => "required|string|unique:equipments,name",
        ]);
        if ($validator->fails()) {
            return [
                "payload" => $validator->errors(),
                "status" => "406_2"
            ];
        }
        $profileGroup=ProfileGroup::find($request->profile_group_id);
        if(!$profileGroup){
            return [
                "payload"=>"profile group is not exist !",
                "status"=>"profile_group_404",
            ];
        }
        $equipment=Equipment::make($request->all());
        $equipment->save();
        $equipment->profileGroup=$equipment->profileGroup;
        $equipment->profileGroup->department=$equipment->profileGroup->department;

        return [
            "payload" => $equipment,
            "status" => "200"
        ];
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            "id" => "required",
        ]);
        if ($validator->fails()) {
            return [
                "payload" => $validator->errors(),
                "status" => "406_2"
            ];
        }
        $equipment=Equipment::find($request->id);
        if (!$equipment) {
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_3"
            ];
        }


        $profileGroup=ProfileGroup::find($request->profile_group_id);
        if(!$profileGroup){
            return [
                "payload"=>"profile group is not exist !",
                "status"=>"profile_group_404",
            ];
        }

        if($request->name!=$equipment->name){
            if(Equipment::where("name",$request->name)->count()>0)
                return [
                    "payload" => "The department has been already taken ! ",
                    "status" => "406_2"
                ];
        }

        $equipment->name=$request->name;

        $equipment->save();
        $equipment->profileGroup=$equipment->profileGroup;
        $equipment->profileGroup->department=$equipment->profileGroup->department;

        return [
            "payload" => $equipment,
            "status" => "200"
        ];
    }

    public function delete(Request $request){
        $equipment=Equipment::find($request->id);
        if(!$equipment){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_4"
            ];
        }
        else {
            $equipment->delete();
            return [
                "payload" => "Deleted successfully",
                "status" => "200_4"
            ];
        }
    }


    public function getEquipmentsByCounters($id){
        $profileGroup=ProfileGroup::find($id);
        if(!$profileGroup){
            return [
                "payload" => "The searched profiel group row does not exist !",
                "status" => "404_4"
            ];
        }

        $equipments=$profileGroup->equipments;

        $counters=[];
        for ($i=0;$i<count($equipments);$i++){
            $damagedCount=0;
            $confirmedCount=0;
            $closedCount=0;
            $damages=$equipments[$i]->damages;
            for ($k=0;$k<count($damages);$k++){
                if($damages[$k]->status=="on progress"){
                    $damagedCount++;
                }
                if($damages[$k]->status=="confirmed"){
                    $confirmedCount++;
                }
                if($damages[$k]->status=="closed"){
                    $closedCount++;
                }
            }
            array_push($counters,[
                "id" => $equipments[$i]->id,
                "nameEquipment" => $equipments[$i]->name,
                "damagedCount" => $damagedCount,
                "confirmedCount" => $confirmedCount,
                "closedCount" => $closedCount,
            ]);
        }


        return [
            "payload" => $counters,
            "status" => "200_1"
        ];

    }


    public function getEquipmentsByCounter($id){
        $equipment=Equipment::find($id);
        if(!$equipment){
            return [
                "payload" => "The searched equipment row does not exist !",
                "status" => "404_4"
            ];
        }

            $damagedCount=0;
            $confirmedCount=0;
            $closedCount=0;
            $damages=$equipment->damages;
            for ($k=0;$k<count($damages);$k++){
                if($damages[$k]->status=="on progress"){
                    $damagedCount++;
                }
                if($damages[$k]->status=="confirmed"){
                    $confirmedCount++;
                }
                if($damages[$k]->status=="closed"){
                    $closedCount++;
                }
            }



        return [
            "payload" => [
                "id" => $equipment->id,
                "nameEquipment" => $equipment->name,
                "damagedCount" => $damagedCount,
                "confirmedCount" => $confirmedCount,
                "closedCount" => $closedCount,
            ],
            "status" => "200_1"
        ];

    }


}
