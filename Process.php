<?php

require_once 'TechAPI/bootstrap.php';

use TechAPI\Api\SendBrandnameOtp;
use TechAPI\Exception as TechException;
use TechAPI\Auth\AccessToken;

require_once('DB.php');
/**
 * Created by PhpStorm.
 * User: PMTB
 * Date: 7/26/2016
 * Time: 3:31 PM
 */
class Process
{

    static public $PRODUCTS = array(
        1 => array(
            'name' => 'iGarten - Dành cho học sinh từ 4 - 6 tuổi',
            'zones' => array(
                HN_ZONE => array(
                    _4_5_THANG => 1,
                    _9_THANG => 1
                ),
                HCM_ZONE => array(
                    _4_5_THANG => 1,
                    _9_THANG => 1
                )
            ),
        ),
        2 => array(
            'name' => 'April English -  Dành cho học sinh từ 6 - 14 tuổi',
            'zones' => array(
                HN_ZONE => array(
                    _3_THANG => 1,
                    _9_THANG => 1,
                    _12_THANG => 1
                ),
                HCM_ZONE => array(
                    _3_THANG => 1,
                    _9_THANG => 1,
                    _12_THANG => 1
                )
            ),
        ),
        3 => array(
            'name' => 'Apax English 4.0 -  Dành cho học sinh từ 12 - 18 tuổi',
            'zones' => array(
                HN_ZONE => array(
                    _5_THANG => 1,
                    _10_THANG => 1,
                ),
                HCM_ZONE => array(
                    _5_THANG => 1,
                    _10_THANG => 1,
                )
            ),
        ),
    );

    static function sendSMS($phone, $codes, $mode=0){
        $arrMessage = array(
            'Phone'      => "$phone",
            'BrandName'  => 'ApaxEnglish',
            'Message'    => $mode?Process::genPresenterSMS($codes):Process::genSMS($codes)
        );

        // Khởi tạo đối tượng API với các tham số phía trên.
        $apiSendBrandname = new SendBrandnameOtp($arrMessage);

        try
        {
            // Lấy đối tượng Authorization để thực thi API
            $oGrantType      = getTechAuthorization();

            // Thực thi API
            $arrResponse     = $oGrantType->execute($apiSendBrandname);

            // kiểm tra kết quả trả về có lỗi hay không
            if (! empty($arrResponse['error']))
            {
                // Xóa cache access token khi có lỗi xảy ra từ phía server
                AccessToken::getInstance()->clear();

                // quăng lỗi ra, và ghi log
                throw new TechException($arrResponse['error_description'], $arrResponse['error']);
            }

//            echo '<pre>';
//            print_r($arrResponse);
//            echo '</pre>';
        }
        catch (\Exception $ex)
        {
//            echo sprintf('<p>Có lỗi xảy ra:</p>');
//            echo sprintf('<p>- Mã lỗi: %s</p>', $ex->getCode());
//            echo sprintf('<p>- Mô tả lỗi: %s</p>', $ex->getMessage());
        }
    }
    
    static function resendSMS($phone, $codes, $mode=0){
        $arrMessage = array(
            'Phone'      => "$phone",
            'BrandName'  => 'ApaxEnglish',
            'Message'    => $mode?Process::genRePresenterSMS($codes):Process::genReSMS($codes)
        );

        // Khởi tạo đối tượng API với các tham số phía trên.
        $apiSendBrandname = new SendBrandnameOtp($arrMessage);

        try
        {
            // Lấy đối tượng Authorization để thực thi API
            $oGrantType      = getTechAuthorization();

            // Thực thi API
            $arrResponse     = $oGrantType->execute($apiSendBrandname);

            // kiểm tra kết quả trả về có lỗi hay không
            if (! empty($arrResponse['error']))
            {
                // Xóa cache access token khi có lỗi xảy ra từ phía server
                AccessToken::getInstance()->clear();

                // quăng lỗi ra, và ghi log
                throw new TechException($arrResponse['error_description'], $arrResponse['error']);
            }

            echo '<pre>';
            print_r($arrResponse);
            echo '</pre>';
        }
        catch (\Exception $ex)
        {
            echo sprintf('<p>Có lỗi xảy ra:</p>');
            echo sprintf('<p>- Mã lỗi: %s</p>', $ex->getCode());
            echo sprintf('<p>- Mô tả lỗi: %s</p>', $ex->getMessage());
        }
    }

    static function genSMS($codes){
        return "Chúc mừng Quý khách hàng đã nhận được Mã dự thưởng ".implode(', ',$codes).". Chi tiết truy cập www.apaxenglish.com";
    }

    static function genPresenterSMS($codes){
        return "Chúc mừng Quý khách hàng đã nhận được Mã dự thưởng ".implode(', ',$codes).". Chi tiết truy cập www.apaxenglish.com";
    }
    
    static function genReSMS($codes){
        return "APAX ENGLISH chuc mung Quy khach hang dang ky thanh cong khoa hoc. Ma du thuong chuong trinh Du Xuan Hai Loc Vang cua Quy khach hang la $codes. Quy khach hang vui long den Trung tam Apax English da dang ky de hoan thanh thu tuc. Chi tiet www.apaxenglish.com. Neu quy khach da nhan duoc ma du thuong, vui long bo qua tin nhan nay.";
    }
    
    static function genRePresenterSMS($codes){
        return "APAX ENGLISH chuc mung Quy khach hang da nhan duoc co hoi tham gia Quay so trung thuong. Ma du thuong chuong trinh Du Xuan Hai Loc Vang cua Quy khach hang la $codes. Chi tiet www.apaxenglish.com. Neu quy khach da nhan duoc ma du thuong, vui long bo qua tin nhan nay.";
    }

    static function getColFromCol($currentCol,$offset){
        $columnIndex = PHPExcel_Cell::columnIndexFromString($currentCol);
        $adjustedColumnIndex = $columnIndex + $offset;
        $adjustedColumn = PHPExcel_Cell::stringFromColumnIndex($adjustedColumnIndex - 1);
        return $adjustedColumn;
    }

    static function styleCells($obj,$cells,$bg_color=null,$color=null,$font_size=null,$font_weight=null,$word_wrap=null,$horizontal=null,$vertical=null,$border=true,$index=-1){
        $bg_color = $bg_color?$bg_color:'FFFFFF';
        $color = $color?$color:'000000';
        $font_weight = $font_weight?true:false;
        $font_size = $font_size?$font_size:11;
        $alignment = array();
        switch($horizontal){
            case 'center':
                $alignment['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_CENTER;
                break;
            case 'left':
                $alignment['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_LEFT;
                break;
            case 'justify':
                $alignment['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY;
                break;
            default:
                $alignment['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_RIGHT;
        }
        switch($vertical){
            case 'center':
                $alignment['vertical'] = PHPExcel_Style_Alignment::VERTICAL_CENTER;
                break;
            case 'justify':
                $alignment['vertical'] = PHPExcel_Style_Alignment::VERTICAL_JUSTIFY;
                break;
            case 'bottom':
                $alignment['vertical'] = PHPExcel_Style_Alignment::VERTICAL_BOTTOM;
                break;
            default:
                $alignment['vertical'] = PHPExcel_Style_Alignment::VERTICAL_TOP;
        }

        $style = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array(
                    'rgb' => $bg_color
                ),
            ),
            'font'  => array(
                'bold'  => $font_weight,
                'color' => array('rgb' => $color),
                'size' => $font_size,
                'name' => 'Times New Roman',
            ),
            'alignment' => $alignment,
        );

        if($border !== false){
            $style['borders'] = array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            );
        }
        if($index > -1){
            $obj->setActiveSheetIndex($index)->getStyle($cells)->applyFromArray($style);
            if($word_wrap === true || $word_wrap === false){
                $obj->setActiveSheetIndex($index)->getStyle($cells)->getAlignment()->setWrapText($word_wrap);
            }
        }else{
            $obj->getActiveSheet()->getStyle($cells)->applyFromArray($style);
            if($word_wrap === true || $word_wrap === false){
                $obj->getActiveSheet()->getStyle($cells)->getAlignment()->setWrapText($word_wrap);
            }
        }


    }

    static function renderTitle2($obj,$sheet, $index){
        $currentSheet = $sheet;
        $currentSheet->SetCellValue("A1", "BÁO CÁO DANH SÁCH ĐĂNG KÝ");
        $currentSheet->mergeCells('A1:J1');
        $currentSheet->getRowDimension('1')->setRowHeight(50);
        $currentSheet->SetCellValue("A3","STT");
        $currentSheet->SetCellValue("B3","Học sinh");
        $currentSheet->SetCellValue("C3","Phụ huynh");
        $currentSheet->SetCellValue("D3","Mobile");
        $currentSheet->SetCellValue("E3","Tên người giới thiệu");
        $currentSheet->SetCellValue("F3","SĐT Người giới thiệu");
        $currentSheet->SetCellValue("G3","Trung tâm");
        $currentSheet->SetCellValue("H3","Chương trình học");
        $currentSheet->SetCellValue("I3","Mã dự thưởng");
        $currentSheet->SetCellValue("J3","Hình thức Đăng ký");
        $currentSheet->SetCellValue("K3","Trạng thái");

        $currentSheet->getColumnDimension("A")->setWidth(8);
        $currentSheet->getColumnDimension("B")->setWidth(25);
        $currentSheet->getColumnDimension("C")->setWidth(30);
        $currentSheet->getColumnDimension("D")->setWidth(30);
        $currentSheet->getColumnDimension("E")->setWidth(25);
        $currentSheet->getColumnDimension("F")->setWidth(25);
        $currentSheet->getColumnDimension("G")->setWidth(24);
        $currentSheet->getColumnDimension("H")->setWidth(45);
        $currentSheet->getColumnDimension("I")->setWidth(24);
        $currentSheet->getColumnDimension("J")->setWidth(24);
        $currentSheet->getColumnDimension("K")->setWidth(24);

        self::styleCells($obj,"A1:K1","","",16,1,true,"center","center",true,$index);
        self::styleCells($obj,"A3:K3","","",11,1,true,"center","center",true,$index);
    }
    static function export_data($data, $role){
        require_once('Classes/PHPExcel.php');
        require_once('Classes/PHPExcel/IOFactory.php');
        require_once('Classes/PHPExcel/Writer/Excel2007.php');

        $obj = new PHPExcel();
        $obj->getProperties()->setCreated('PMTB');
        $obj->getDefaultStyle()->getFont()->setName('Times New Roman');

        $sheet_index = 0;
        $row = 4;
        $obj->createSheet($sheet_index);
        $current_sheet = $obj->setActiveSheetIndex($sheet_index);


        Process::renderTitle2($obj,$current_sheet,$sheet_index);

        foreach ($data as $item) {
            $current_sheet->setCellValue("A$row", $row - 2);
            $current_sheet->setCellValue("B$row", $item['name']);
            $current_sheet->setCellValue("C$row", $item['parent']);
	    $phone = '';
 	    if ($role == 1) $phone = $item['phone'];
            $current_sheet->setCellValue("D$row", $phone);
            $current_sheet->setCellValue("E$row", $item['presenter_name']);
	    $parent_phone = '';
	    if ($role == 1) $parent_phone = $item['presenter_phone'];
            $current_sheet->setCellValue("F$row", $parent_phone);
            $current_sheet->setCellValue("G$row", $item['center_name']);
	    $product = Process::$PRODUCTS[$item['product_id']]['name'];       
		$current_sheet->setCellValue("H$row", $product);
            $current_sheet->setCellValue("I$row", $item['code']);
            $htdk ='';
            if ($item['mode'] == 1){
                $htdk = 'Offline';
            }
            else if ($item['mode'] == 2){
                $htdk = 'Facebook';
            }
            else $htdk= 'Online';
            $current_sheet->setCellValue("J$row", $htdk);
            $status = '';
            if ($item['status'] == 0){
                $status = 'Chưa liên hệ';
            }
            else if ($item['status'] == 1){
                $status = "Đã liên hệ";
            }
            else $status = "Đã xác nhận nộp tiền";
            $current_sheet->setCellValue("K$row", $status);
            self::styleCells($obj,"A$row","","",11,'','',"center","center",true);
            self::styleCells($obj,"B$row:C$row","","",11,'','',"left","center",true);
            self::styleCells($obj,"D$row","","",11,'','',"center","center",true);
            self::styleCells($obj,"E$row:G$row",'',"",11,'','',"left","center",true);
            self::styleCells($obj,"G$row:K$row","","",11,'','',"center","center",true);
            $row++;
        }



        $objWriter = PHPExcel_IOFactory::createWriter($obj, 'Excel2007');
        $objWriter->setPreCalculateFormulas();


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Danh sách đăng ký.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
        exit;
    }

    static function export($data, $centers, $cities){
        require_once('Classes/PHPExcel.php');
        require_once('Classes/PHPExcel/IOFactory.php');
        require_once('Classes/PHPExcel/Writer/Excel2007.php');

        $obj = new PHPExcel();
        $obj->getProperties()->setCreated('PMTB');
        $obj->getDefaultStyle()->getFont()->setName('Times New Roman');

        $sheet_index = 0;
        $row = 2;
        $obj->createSheet($sheet_index);
        $current_sheet = $obj->setActiveSheetIndex($sheet_index);


        Process::renderTitle2($obj,$current_sheet,$sheet_index);

//        die;


        foreach ($data as $da){
            $phone = $da['phone'];

            $current_sheet->setCellValue("A$row", $row - 1);
            $current_sheet->setCellValue("B$row", date("d/m/Y H:i",$da['created_time']));
            $current_sheet->setCellValue("C$row", $da['name']);
            $current_sheet->setCellValue("D$row", $da['parent']);
            $current_sheet->setCellValue("E$row", "'$phone");
            $current_sheet->setCellValue("F$row", $centers[$da['center']]['address']);
            $current_sheet->setCellValue("G$row", Process::$PRODUCTS[$da['product_id']]['name']);
            $current_sheet->setCellValue("H$row", $da['tuition_fee']/10 . ' tháng');
            $row++;
        }

        self::styleCells($obj,"A2:A$row",null,null,11,null,true,"center","center",true,$sheet_index);
        self::styleCells($obj,"B2:G$row",null,null,11,null,true,"left","center",true,$sheet_index);
        self::styleCells($obj,"H2:H$row",null,null,11,null,true,"center","center",true,$sheet_index);



        $objWriter = PHPExcel_IOFactory::createWriter($obj, 'Excel2007');
        $objWriter->setPreCalculateFormulas();


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Danh sách đăng ký.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
        exit;
    }

    static function confirmByselect($status, $key){
        $time = (int)microtime(true);
        $member = DB::fetch("SELECT *from events_register WHERE created_time = $key");
        $center_id = $member['center'];
        $center = DB::fetch("SELECT *from agents where id = $center_id");

        if ($status == 1){
              DB::query("UPDATE events_register SET status = 1 where created_time = $key");
        }
        else{
            if ($member['tuition_fee']){
            $total_code = Process::$PRODUCTS[$member['product_id']]['zones'][$center['zone_id']][$member['tuition_fee']];
            $member['tuition_fee'];

            $codes = Process::getCodes($member['center'],$total_code);
            $member_codes =  implode(', ',$codes['codes']);
            $id_code = implode(',', $codes['ids']);

            DB::query("UPDATE codes SET connection_key = $time, status = 2 where id in ($id_code)");
            DB::query("UPDATE events_register SET connection_key = '$time', code = '$member_codes', status=2 where created_time = $key");
            
            Process::sendSMS($member['phone'],$codes['codes']); 

            $phone_parent = $member['presenter_phone'];

            if ($phone_parent){
                $parent = DB::fetch("SELECT *from events_register where phone = '$phone_parent' LIMIT 1");
                if ($parent){
                    $code_parent = Process::getCodes($parent['center'],1);
                    $t = (int)microtime(true);
                    $code_id = $code_parent['ids'][0];
                    $newcode = $parent['code'].','.$code_parent['codes'][0];
                    DB::query("UPDATE codes set connection_key = '$t', status = 1 where id = $code_id");
                    DB::query("UPDATE events_register set connection_key = '$t', code = '$newcode', mode = 3 where phone = '$phone_parent'");

                    Process::sendSMS($parent['phone'],$code_parent['codes']); 
                }
                
            } 
            }else {
                return "tuition_fee is not found";
            }
        }
    }

    static function confirmRegister($key){
        $time = (int)microtime(true);
        $member = DB::fetch("SELECT *from events_register WHERE created_time = $key");
        
        // $product_id = $member[6500]['product_id'];
        // $tuition_fee = $member[6500]['tuition_fee'];
        // reset(Process::$PRODUCTS[$product]['zones'][$centers[$center_id]['zone_id']]);
        $center_id = $member['center'];
        $center = DB::fetch("SELECT *from agents where id = $center_id");
        if ($member['status'] == 0){
            DB::query("UPDATE events_register SET status = 1 where created_time = $key");
        }else{
            if ($member['tuition_fee']){
            $total_code = Process::$PRODUCTS[$member['product_id']]['zones'][$center['zone_id']][$member['tuition_fee']];
            $member['tuition_fee'];

            $codes = Process::getCodes($member['center'],$total_code);
            $member_codes =  implode(', ',$codes['codes']);
            $id_code = implode(',', $codes['ids']);

            DB::query("UPDATE codes SET connection_key = $time, status = 2 where id in ($id_code)");
            DB::query("UPDATE events_register SET connection_key = '$time', code = '$member_codes', status=2 where created_time = $key");
            
            Process::sendSMS($member['phone'],$codes['codes']); 

            $phone_parent = $member['presenter_phone'];

            if ($phone_parent){
                $parent = DB::fetch("SELECT *from events_register where phone = '$phone_parent' LIMIT 1");
                if ($parent){
                    $code_parent = Process::getCodes($parent['center'],1);
                    $t = (int)microtime(true);
                    $code_id = $code_parent['ids'][0];
                    $newcode = $parent['code'].','.$code_parent['codes'][0];
                    DB::query("UPDATE codes set connection_key = '$t', status = 1 where id = $code_id");
                    DB::query("UPDATE events_register set connection_key = '$t', code = '$newcode', mode = 3 where phone = '$phone_parent'");

                    Process::sendSMS($parent['phone'],$code_parent['codes']); 
                }
                
            } 
            }else {
                return "tuition_fee is not found";
            }
        }

    }

    static function registerEvent($data){
        DB::insert_v2('events_register', $data);
    }

    static function getCodes($center_id, $code_number){
        $codes = DB::fetch_all("SELECT id, code FROM codes WHERE center=$center_id AND status=0 AND code like '%DL%' LIMIT 0,$code_number");
        $res = array(
            'codes' => array(),
            'ids' => array()
        );
        foreach ($codes as $code){
            $res['codes'][] = $code['code'];
            $res['ids'][] = $code['id'];
        }
        return $res;
    }

    static function delete($id = null){
        DB::delete_id('events_register', $id);
    }

}