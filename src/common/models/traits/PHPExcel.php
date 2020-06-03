<?php

namespace common\models\traits;

trait PHPExcel
{
    public function exportDatas($datas, $title, $outFile = 'php://output')
    {   
        if (empty($datas)) { 
            return ['status' => '400', 'data empty'];
        }
        if (count($datas) > 10000) {
            return ['status' => '400', 'data over 10000'];
        }
        
        //$objPHPExcel = new \PHPExcel\Spreadsheet();
        $objPHPExcel = new \PHPExcel();
        
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Creator")
                                     ->setLastModifiedBy("System")
                                     ->setTitle($title)
                                     ->setSubject($title)
                                     ->setDescription($title)
                                     ->setKeywords("office 2007 openxml php")
                                     ->setCategory("Test result file");
        
        
        $objPHPExcel = $this->_formatExportDatas($objPHPExcel, $datas);
        
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle($title);
        
        
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        
        
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $title . '.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($outFile);
		return ;
    }
      
    public function importDatas($file)
    {
        //$objPHPExcel = \PHPExcel\IOFactory::load($file);
		$readers = ['PHPExcel_Reader_Excel2007', 'PHPExcel_Reader_Excel5', 'PHPExcel_Reader_CSV'];
		$readerObj = null;
		foreach ($readers as $reader) {
			$obj = new $reader();
			if ($obj->canRead($file)) {
				$readerObj = $obj;
				break;
			}
		}
		if (empty($readerObj)) {
			return ['status' => 400, 'message' => '无法读取文档内容'];
		}

        $datas = [];
		$content = $readerObj->load($file);
        $sCount = $content->getSheetCount();
        for ($i = 0; $i < $sCount; $i++) {
            $datas[$i] = $content->getSheet($i)->toArray(null,true,true,true);
        }

		return $datas;
    }
}
