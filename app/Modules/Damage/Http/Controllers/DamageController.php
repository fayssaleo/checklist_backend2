<?php

namespace App\Modules\Damage\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Libs\UploadTrait;
use App\Modules\Damage\Models\Damage;
use App\Modules\Damage\Models\Photo;
use App\Modules\DamageType\Models\DamageType;
use App\Modules\Equipment\Models\Equipment;
use App\Modules\ProfileGroup\Models\ProfileGroup;
use App\Modules\User\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DamageController extends Controller
{
    use UploadTrait;

    public function index(){

        $damages=Damage::with("declaredBy.fonction.department")
            ->with("confirmedBy.fonction.department")
            ->with("closedBy.fonction.department")
            ->with("revertedBy.fonction.department")
            ->with("equipment.profileGroup.department")
            ->with("damageType","damageType.profileGroup.department","damageType.department")
            ->with("photos")
            ->get();

        return [
            "payload" => $damages,
            "status" => "200_00"
        ];
    }
    //declareDamage
    public function declareDamage(Request $request){
        $declaredDamages=[];
        for ($i=0;$i<count($request->damages);$i++){
            $declaredBy=User::find($request->damages[$i]["declaredBy_id"]);
            if(!$declaredBy){
                return [
                    "payload"=>"user is not exist !",
                    "status"=>"user_404",
                ];
            }

            $damageType=DamageType::find($request->damages[$i]["damage_type_id"]);
            if(!$damageType){
                return [
                    "payload"=>"damage type is not exist !",
                    "status"=>"damage_type_404",
                ];
            }

            $equipment=Equipment::find($request->damages[$i]["equipment_id"]);
            if(!$equipment){
                return [
                    "payload"=>"equipment is not exist !",
                    "status"=>"equipment_404",
                ];
            }
            $existDamage=Damage::select()
                ->where([
                    ['damage_type_id', '=', $request->damages[$i]["damage_type_id"]],
                    ['equipment_id', '=', $request->damages[$i]["equipment_id"]],
                    ['status', '=', "confirmed"],
                ])
                ->orWhere([
                    ['damage_type_id', '=', $request->damages[$i]["damage_type_id"]],
                    ['equipment_id', '=', $request->damages[$i]["equipment_id"]],
                    ['status', '=', "on progress"],
                ])
                ->first();
            if($existDamage){
                return [
                    "payload"=>"The ".$damageType->name." damage is already ".$existDamage->status." !",
                    "status"=>"400",
                ];
            }
            $damage=Damage::make($request->damages[$i]);
            $damage->declaredAt=Carbon::now();
            $damage->save();
            $damage->declaredBy=$damage->declaredBy()->with("fonction.department")->first();
            $damage->confirmedBy=$damage->confirmedBy()->with("fonction.department")->first();
            $damage->closedBy=$damage->closedBy()->with("fonction.department")->first();
            $damage->revertedBy=$damage->revertedBy()->with("fonction.department")->first();
            $damage->equipment=$damage->equipment()->with("profileGroup.department")->first();
            $damage->damageType=$damage->damageType()->with("profileGroup.department")->with("department")->first();
            $damage->photos=$damage->photos;
            array_push($declaredDamages,$damage);
        }


        return [
            "payload" => $declaredDamages,
            "status" => "200"
        ];
    }

    public function confirmDamage(Request $request){
        $validator = Validator::make($request->all(), [
            "id" => "required",
            "confirmedBy_id" => "required",
        ]);
        if ($validator->fails()) {
            return [
                "payload" => $validator->errors(),
                "status" => "406_2"
            ];
        }
        $confirmedBy=User::find($request->confirmedBy_id);
        if(!$confirmedBy){
            return [
                "payload"=>"user is not exist !",
                "status"=>"user_404",
            ];
        }


        $damage=Damage::find($request->id);
        if(!$damage){
            return [
                "payload"=>"damage is not exist !",
                "status"=>"damage_404",
            ];
        }


        if($damage->status!="on progress"){
            return [
                "payload"=>"damage is not on progress to be confirmed !",
                "status"=>"damage_400",
            ];
        }

        $damage->status="confirmed";
        $damage->confirmedBy_id=$confirmedBy->id;
        $damage->confirmedAt=Carbon::now();
        $damage->save();
        $damage->declaredBy=$damage->declaredBy()->with("fonction.department")->first();
        $damage->confirmedBy=$damage->confirmedBy()->with("fonction.department")->first();
        $damage->closedBy=$damage->closedBy()->with("fonction.department")->first();
        $damage->revertedBy=$damage->revertedBy()->with("fonction.department")->first();
        $damage->equipment=$damage->equipment()->with("profileGroup.department")->first();
        $damage->damageType=$damage->damageType()->with("profileGroup.department")->with("department")->first();
        $damage->photos=$damage->photos;
        return [
            "payload" => $damage,
            "status" => "200"
        ];
    }

    public function closeDamage(Request $request){
        $validator = Validator::make($request->all(), [
            "id" => "required",
            "closedBy_id" => "required",
        ]);
        if ($validator->fails()) {
            return [
                "payload" => $validator->errors(),
                "status" => "406_2"
            ];
        }
        $closedBy=User::find($request->closedBy_id);
        if(!$closedBy){
            return [
                "payload"=>"user is not exist !",
                "status"=>"user_404",
            ];
        }


        $damage=Damage::find($request->id);
        if(!$damage){
            return [
                "payload"=>"damage is not exist !",
                "status"=>"damage_404",
            ];
        }

        if($damage->status!="confirmed"){
            return [
                "payload"=>"damage is not confirmed to be closed !",
                "status"=>"damage_400",
            ];
        }

        $damage->status="closed";
        $damage->closedBy_id=$closedBy->id;
        $damage->closedAt=Carbon::now();
        $damage->save();
        $damage->declaredBy=$damage->declaredBy()->with("fonction.department")->first();
        $damage->confirmedBy=$damage->confirmedBy()->with("fonction.department")->first();
        $damage->closedBy=$damage->closedBy()->with("fonction.department")->first();
        $damage->revertedBy=$damage->revertedBy()->with("fonction.department")->first();
        $damage->equipment=$damage->equipment()->with("profileGroup.department")->first();
        $damage->damageType=$damage->damageType()->with("profileGroup.department")->with("department")->first();
        $damage->photos=$damage->photos;
        return [
            "payload" => $damage,
            "status" => "200"
        ];
    }

    public function revertDamage(Request $request){
        $validator = Validator::make($request->all(), [
            "id" => "required",
            "revertedBy_id" => "required",
        ]);
        if ($validator->fails()) {
            return [
                "payload" => $validator->errors(),
                "status" => "406_2"
            ];
        }
        $revertedBy=User::find($request->revertedBy_id);
        if(!$revertedBy){
            return [
                "payload"=>"user is not exist !",
                "status"=>"user_404",
            ];
        }


        $damage=Damage::find($request->id);
        if(!$damage){
            return [
                "payload"=>"damage is not exist !",
                "status"=>"damage_404",
            ];
        }

        if($damage->status!="confirmed"){
            return [
                "payload"=>"damage is not confirmed to be reverted !",
                "status"=>"damage_400",
            ];
        }

        $damage->status="on progress";
        $damage->revertedBy_id=$revertedBy->id;
        $damage->revertedAt=Carbon::now();
        $damage->revertedTimes=++$damage->revertedTimes;
        $damage->save();
        $damage->declaredBy=$damage->declaredBy()->with("fonction.department")->first();
        $damage->confirmedBy=$damage->confirmedBy()->with("fonction.department")->first();
        $damage->closedBy=$damage->closedBy()->with("fonction.department")->first();
        $damage->revertedBy=$damage->revertedBy()->with("fonction.department")->first();
        $damage->equipment=$damage->equipment()->with("profileGroup.department")->first();
        $damage->damageType=$damage->damageType()->with("profileGroup.department")->with("department")->first();
        $damage->photos=$damage->photos;
        return [
            "payload" => $damage,
            "status" => "200"
        ];
    }







    public function getDamagesByProfileGroup($id){
        $profielGroup=ProfileGroup::select()->where('id', $id)->with("equipments")->first();
        if(!$profielGroup){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }


        else {
            $damages=[];
            for ($x = 0; $x < count($profielGroup->equipments); $x++) {
                $thisDamages=$profielGroup->equipments[$x]->damages()
                    ->with("declaredBy.fonction.department")
                    ->with("confirmedBy.fonction.department")
                    ->with("closedBy.fonction.department")
                    ->with("revertedBy.fonction.department")
                    ->with("equipment.profileGroup.department")
                    ->with("damageType","damageType.profileGroup.department","damageType.department")
                    ->with("photos")
                    ->get()
                    ->toArray();
                $damages=array_merge($damages,$thisDamages);
            }
            return [
                "payload" => $damages,
                "status" => "200_1"
            ];
        }
    }


    public function getDamagesByEquipments($id){
        $equipment=Equipment::select()->where('id', $id)->with("profileGroup")->first();
        if(!$equipment){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }
        else {
            return [
                "payload" => $equipment->damages()->with("declaredBy.fonction.department")
                    ->with("confirmedBy.fonction.department")
                    ->with("closedBy.fonction.department")
                    ->with("revertedBy.fonction.department")
                    ->with("equipment.profileGroup.department")
                    ->with("damageType","damageType.profileGroup.department","damageType.department")
                    ->with("photos")
                    ->get(),
                "status" => "200_1"
            ];
        }
    }


    public function getDamagesByDeclareds($id){
        $declaredBy=User::select()->where('id', $id)->with("fonction")->first();
        if(!$declaredBy){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }
        else {
            return [
                "payload" => $declaredBy->declaredBys()->with("declaredBy.fonction.department")
                    ->with("confirmedBy.fonction.department")
                    ->with("closedBy.fonction.department")
                    ->with("revertedBy.fonction.department")
                    ->with("equipment.profileGroup.department")
                    ->with("damageType","damageType.profileGroup.department","damageType.department")
                    ->with("photos")
                    ->get(),
                "status" => "200_1f"
            ];
        }
    }

    public function getDamagesByConfirmeds($id){
        $declaredBy=User::select()->where('id', $id)->with("fonction")->first();
        if(!$declaredBy){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }
        else {
            return [
                "payload" => $declaredBy->confirmedBys()->with("declaredBy.fonction.department")
                    ->with("confirmedBy.fonction.department")
                    ->with("closedBy.fonction.department")
                    ->with("revertedBy.fonction.department")
                    ->with("equipment.profileGroup.department")
                    ->with("damageType","damageType.profileGroup.department","damageType.department")
                    ->with("photos")
                    ->get(),
                "status" => "200_1f"
            ];
        }
    }

    public function getDamagesByCloseds($id){
        $declaredBy=User::select()->where('id', $id)->with("fonction")->first();
        if(!$declaredBy){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }
        else {
            return [
                "payload" => $declaredBy->closedBys()->with("declaredBy.fonction.department")
                    ->with("confirmedBy.fonction.department")
                    ->with("closedBy.fonction.department")
                    ->with("revertedBy.fonction.department")
                    ->with("equipment.profileGroup.department")
                    ->with("damageType","damageType.profileGroup.department","damageType.department")
                    ->with("photos")
                    ->get(),
                "status" => "200_1f"
            ];
        }
    }

    public function getDamagesByReverteds($id){
        $declaredBy=User::select()->where('id', $id)->with("fonction")->first();
        if(!$declaredBy){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }
        else {
            return [
                "payload" => $declaredBy->revertedBys()->with("declaredBy.fonction.department")
                    ->with("confirmedBy.fonction.department")
                    ->with("closedBy.fonction.department")
                    ->with("revertedBy.fonction.department")
                    ->with("equipment.profileGroup.department")
                    ->with("damageType","damageType.profileGroup.department","damageType.department")
                    ->with("photos")
                    ->get(),
                "status" => "200_1f"
            ];
        }
    }

    public function getEquipmentDamagesMergedWithDamageTypes($id){
        $equipment=Equipment::select()->where('id', $id)->with("profileGroup")->first();
        if(!$equipment){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }
        else {
            $damaeTypeWithDamages=[];
            $profileGroupDamageTypes=$equipment->profileGroup->damageTypes()->get();

            for ($i=0;$i<count($profileGroupDamageTypes);$i++){
                $profileGroupDamageTypes[$i]->damage=Damage::select()
                    ->where([
                        ['damage_type_id', '=', $profileGroupDamageTypes[$i]->id],
                        ['equipment_id', '=', $equipment->id],
                        ['status', '=', "confirmed"],
                    ])
                    ->orWhere([
                        ['damage_type_id', '=', $profileGroupDamageTypes[$i]->id],
                        ['equipment_id', '=', $equipment->id],
                        ['status', '=', "on progress"],
                    ])
                    ->with("declaredBy.fonction.department")
                    ->with("confirmedBy.fonction.department")
                    ->with("closedBy.fonction.department")
                    ->with("revertedBy.fonction.department")
                    ->with("equipment.profileGroup.department")
                    ->with("damageType","damageType.profileGroup.department","damageType.department")
                    ->with("photos")
                    ->first();
                array_push($damaeTypeWithDamages,$profileGroupDamageTypes[$i]);
            }
            return [
                "payload" => $damaeTypeWithDamages,
                "status" => "200_1"
            ];
        }
    }

    public function foremanIntervention(Request $request){
        $validator = Validator::make($request->all(), [
            "id" => "required",
        ]);
        if ($validator->fails()) {
            return [
                "payload" => $validator->errors(),
                "status" => "406_2"
            ];
        }

        $foreman=User::find($request->foreman_id);
        if(!$foreman){
            return [
                "payload"=>"user is not exist !",
                "status"=>"user_404",
            ];
        }


        $damage=Damage::find($request->id);
        if(!$damage){
            return [
                "payload"=>"damage is not exist !",
                "status"=>"damage_404",
            ];
        }

        $damage->description=$request->description;
        $damage->save();

        if($request->file()) {
            for ($i=0;$i<count($request->photos);$i++){
                $file=$request->photos[$i];
                $filename=time()."_".$file->getClientOriginalName();
                $this->uploadOne($file, config('cdn.damagePhotos.path'),$filename);
                $photo=new Photo();
                $photo->filename=$filename;
                $photo->damage_id=$damage->id;
                $photo->save();
            }
        }
        $damage->photos=$damage->photos;
        return [
            "payload"=>$damage,
            "status"=>"200_04",
        ];
    }

    public function sendDamagePhotosStoragePath(){
        return [
            "payload" => asset("/storage/cdn/damagePhotos/"),
            "status" => "200_1"
        ];
    }

}
