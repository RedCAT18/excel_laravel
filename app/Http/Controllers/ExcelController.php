<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  Maatwebsite\Excel\Facades\Excel;
use App\User;
use App\Menu;

class ExcelController extends Controller
{
    //
    public function loadExcel(Request $request){

        try{
            if($request -> hasFile('file')){
                //파일이 excel인지 아닌지 확인
                $uploadedFileType = $request->file('file')->getClientOriginalExtension();
//                return response($uploadedFileType);
                //확장자가 xlsx나 xls가 아니면 에러메세지 출력.
                if( $uploadedFileType != ('xlsx' || 'xls')){
                    return response('Wrong file type is sent.');
                }
                //파일명은 날짜_시간_본래파일명해싱으로 생성한다.
                //사실 이렇게만 해도 중복파일명은 안 나올 거 같지만 사용자가 수만명을 넘어간다면 인생... 모르죠...
                $createdName = date("Ymd_His").'_'.md5($request->file('file')->getClientOriginalName()); //생성된 파일명만 따로 저장.
//                $fileName = 'excel_file.' .$request->file('file')->getClientOriginalExtension();
                $fileName = $createdName.'.'.$uploadedFileType;

                // 폴더 : 디렉토리를 검사해 해당 폴더가 있는지 보고 없으면 생성, 있으면 그대로 진행.
                $filePath = base_path().'/public/uploads/';
                if(!file_exists($filePath)){
                    mkdir($filePath, 0777);
                }

                // 파일: 1. 폴더 안의 파일 중에 이름이 동일한 파일이 있는가 검색한다.
                //       2. 있을 시, 다시 한 번 이름 생성. 없을시 그대로 저장.
                if(file_exists($filePath.$fileName)){
//                    $fileName = substr($fileName, 0, strrpos($fileName, ".")).'_'.mt_rand(1,10000).'.'.strtolower(substr(strrchr($fileName, "."),1));
                    //같은 이름이 있을 시에 본래 파일명에다 1~10000사이의 랜덤수를 생성해서 붙여주는 방식을 택했는데...
                    //파일명과 확장자를 분리한 뒤 그 사이에 생성숫자를 넣고 다시 합치니까 정말 코드가 더럽다......는 내탓이 아니야!!!!
                    //어차피 위에 생성된 파일명이랑 확장자가 각각 변수로 존재하니까 재활용한다 <<
//                    return response('same '.$filePath.$fileName);
                    $fileName = $createdName.'_'.mt_rand(1,10000).'.'.$uploadedFileType;
                }
//                return response('no same '.$filePath.$fileName);
                $path = $request->file('file')->move($filePath, $fileName);

                //저장된 엑셀 데이터 가져오기
                Excel::load($path, function($render){
                    $results = $render->skipRows(0)->get();
                    $rows = $results->toArray();

                    //null이 아닐때 저장.
                    foreach($rows as $r){
                        if($r !== null){
                            $user = new User();
                            $user->name = $r['name'];
                            $user->email = $r['email'];
                            $user->mobile = $r['mobile'];

                            $user -> save();
                        }
                    }
                });
            }
        }catch(\Exception $e){
            return response()->json(['error'=>$e->getLine(). " : " . $e->getMessage()]);
        }

    }

    public function loadUser(Request $request){

        $user = User::all();

        return $user;

    }

    public function loadMenu(Request $request){
        $menu = Menu::all();
        return $menu;
    }
}
