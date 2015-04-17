<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2012                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2012
 * $Id$
 *
 */

/**
 * This class handle grant related functions
 *
 */
class CRM_Grant_Form_Task_PrintPDF extends CRM_Grant_Form_Task {
  /**
   * build all the data structures needed to build the form
   *
   * @return void
   * @access public
   */
  function preProcess() {
    parent::preprocess();

    // set print view, so that print templates are called
    $this->controller->setPrint(1);

    // get the formatted params
    $queryParams = $this->get('queryParams');

    $sortID = NULL;
    if ($this->get(CRM_Utils_Sort::SORT_ID)) {
      $sortID = CRM_Utils_Sort::sortIDValue($this->get(CRM_Utils_Sort::SORT_ID),
        $this->get(CRM_Utils_Sort::SORT_DIRECTION)
      );
    }

    $selector = new CRM_Grant_Selector_Search($queryParams, $this->_action, $this->_componentClause);
    $controller = new CRM_Core_Selector_Controller($selector, NULL, $sortID, CRM_Core_Action::VIEW, $this, CRM_Core_Selector_Controller::SCREEN);
    $controller->setEmbedded(TRUE);
    $controller->run();
  }

   /**
   * Build the form
   *
   * @access public
   *
   * @return void
   */
  function buildQuickForm() {
    // Process grants and assign to TPL 
    $grantIds = $this->getVar('_grantIds');
    $config = CRM_Core_Config::singleton();
    foreach ($grantIds as $gid) {
      $fileArray = array();
      $values = array();
      $params['id'] = $gid;
      CRM_Grant_BAO_Grant::retrieve($params, $values);
      $values['attachment'] = CRM_Core_BAO_File::getEntityFile('civicrm_grant', $gid);
      $custom = CRM_Core_BAO_CustomValueTable::getEntityValues($gid, 'Grant');
      $ids = array_keys($custom);
      $count = 0;
      foreach( $ids as $key => $val ) {
        $customData[$count]['label'] = CRM_Core_DAO::getFieldValue("CRM_Core_DAO_CustomField", $val, "label", "id");
        $customData[$count]['html_type'] = CRM_Core_DAO::getFieldValue("CRM_Core_DAO_CustomField", $val, "html_type", "id");
        $customData[$count]['data_type'] = CRM_Core_DAO::getFieldValue("CRM_Core_DAO_CustomField", $val, "data_type", "id");
        $cfParams = array('id' => $val);
        $cfDefaults = array();
        CRM_Core_DAO::commonRetrieve('CRM_Core_DAO_CustomField', $cfParams, $cfDefaults);
        $columnName = $cfDefaults['column_name'];
        
        //table name of custom data
        $tableName = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_CustomGroup',
                                                 $cfDefaults['custom_group_id'],
                                                 'table_name', 'id'
                                                 );
        
        //query to fetch id from civicrm_file
        $query = "SELECT {$columnName} FROM {$tableName} where entity_id = {$gid}";
        $fileID = CRM_Core_DAO::singleValueQuery($query);
        $customData[$count]['value'] = $fileID;
        $count++;
      }
      if (isset($gid)) {
        $noteDAO = new CRM_Core_BAO_Note();
        $noteDAO->entity_table = 'civicrm_grant';
        $noteDAO->entity_id = $gid;
        if ($noteDAO->find(TRUE)) {
          $values['noteId'] = $noteDAO->note;
        }
      }
      $values['display_name'] = CRM_Contact_BAO_Contact::displayName($values['contact_id']);
      $values['application_received_date'] = isset($values['application_received_date']) ? date('jS F Y', strtotime($values['application_received_date'])) : "";
      $values['decision_date'] = isset($values['decision_date']) ? date('jS F Y', strtotime($values['decision_date'])) : "";
      $values['money_transfer_date'] = isset($values['money_transfer_date']) ? date('jS F Y', strtotime($values['money_transfer_date'])) : "";
      $values['grant_due_date'] = isset($values['grant_due_date']) ? date('jS F Y', strtotime($values['grant_due_date'])) : "";
      $values['amount_total'] = isset($values['amount_total']) ? $values['amount_total'] : '0.00';
      $values['amount_requested'] = isset($values['amount_requested']) ? $values['amount_requested'] : '0.00';
      $values['amount_granted'] = isset($values['amount_granted']) ? $values['amount_granted'] : '0.00';
      if (!empty($customData)) {
        $fileDAO = new CRM_Core_BAO_File();
        foreach( $customData as $keys => $vals ) {
          if (($vals['html_type'] == "Text" || 
               $vals['html_type'] == "Autocomplete-Select" || 
               $vals['html_type'] == "Radio"|| 
               $vals['html_type'] == "Select Date") && $vals['data_type'] != "ContactReference") {
            $values['custom'][$keys]['label'] = $vals['label'];
            $values['custom'][$keys]['value'] = $vals['value'];
          } 
          elseif (($vals['html_type'] == "AdvMulti-Select" || 
                   $vals['html_type'] == "Multi-Select" || 
                   $vals['html_type'] == "CheckBox" ) && !empty($vals['value'])) {
            $key = explode(CRM_Core_DAO::VALUE_SEPARATOR, $vals['value']);
            $key = array_filter($key);
            $key = implode(', ', $key);
            $values['custom'][$keys]['label'] = $vals['label'];
            $values['custom'][$keys]['value'] = $key;
          } 
          elseif ($vals['data_type'] == "ContactReference" && !empty($vals['value'])) {
            $values['custom'][$keys]['label'] = $vals['label'];
            $values['custom'][$keys]['value'] = CRM_Contact_BAO_Contact::displayName($vals['value']);
          } 
          elseif ($vals['html_type'] == "RichTextEditor") {
            $values['custom'][$keys]['label'] = $vals['label'];
            $values['custom'][$keys]['value'] = strip_tags($vals['value']);
          } 
          elseif ( $vals['html_type'] == "File" ) {
            $fileDAO->id = $vals['value'];
            if( $fileDAO->find(true) ) {
              $source = CRM_Utils_System::url("civicrm/file", "reset=1&eid=$gid&id=$fileDAO->id", TRUE, NULL, FALSE);
              switch( $fileDAO->mime_type ) {
              case "text/plain":
                $raw = file($source);
                $data = implode('<br>', $raw);
                // $html .="<tr><td><b>".$vals['label']."<b></td><td>".$data."</td></tr>";
                $values['custom'][$keys]['label'] = $vals['label'];
                $values['custom'][$keys]['value'] = $data;
                break;
              case "image/jpeg":
              case "image/png":
                // $html .="<tr><td><b>".$vals['label']."<b></td><td><img src='".$source."' /></td></tr>";
                $values['custom'][$keys]['label'] = $vals['label'];
                $values['custom'][$keys]['value'] = "<img src='".$source."' />";
              break;
              case "application/rtf":
                $raw = file($source);
                foreach ( $raw as $plain ) {
                  $text[] = strip_tags($plain);
                }
                $data = implode('<br>', $text);
                $html .="<tr><td><b>Attachment<b></td><td>".$data."</td></tr>";
                $values['custom'][$keys]['label'] = 'Attachment';
                $values['custom'][$keys]['value'] = $data;
                break;
              case "application/msword":
                if (!file_exists($config->customFileUploadDir.$fileDAO->uri)) {
                  break;
                }
                $originFilePath = $config->customFileUploadDir.$fileDAO->uri;
                $outputDirPath  = $config->customFileUploadDir;
                CRM_Unoconv_Unoconv::convertToPdf($originFilePath, $outputDirPath);
                $fileArray[] = $outputDirPath . str_replace('.doc', '.pdf', $fileDAO->uri);  
                break;
              case "application/vnd.ms-excel":
                if (!file_exists($config->customFileUploadDir.$fileDAO->uri)) {
                  break;
                }
                $fileArray[] = self::convertXLStoPDF($fileDAO, 'custom');
              default:
                break;
              }
            }
          }
        }
      } 
      if (!empty($values['attachment'])) {
        foreach( $values['attachment'] as $attachKey => $attachValue ) {
          switch( $attachValue['mime_type'] ) {
          case "image/jpeg":
          case "image/png":
            $source = CRM_Utils_System::url("civicrm/file", "reset=1&eid=".$gid."&id=".$attachValue['fileID']."", TRUE, NULL, FALSE);
            $values['attach'][$keys]['label'] = 'Attachment';
            $values['attach'][$keys]['value'] = "<img src='".$source."' />";
          break;
          case "text/plain":
            $raw = file($attachValue['fullPath']);
            $data = implode('<br>', $raw);
            $values['attach'][$keys]['label'] = 'Attachment';
            $values['attach'][$keys]['value'] = $data;
            break;
          case "application/rtf":
            $raw = file($attachValue['fullPath']);
            foreach ( $raw as $plain ) {
              $text[] = strip_tags($plain);
            }
            $data = implode('<br>', $text);
            $values['attach'][$keys]['label'] = 'Attachment';
            $values['attach'][$keys]['value'] = $data;
            break;
          case "application/msword":
            if (!file_exists($attachValue['fullPath'])) {
              break;
            }
            $originFilePath = $attachValue['fullPath'];
            $outputDirPath  = $config->customFileUploadDir;
            CRM_Unoconv_Unoconv::convertToPdf($originFilePath, $outputDirPath);
            $fileArray[] = $outputDirPath . str_replace('.doc', '.pdf', $attachValue['fileName']);
            break;
          case "application/vnd.ms-excel":
            if (!file_exists($attachValue['fullPath'])) {
              break;
            }
            $fileArray[] = self::convertXLStoPDF($attachValue, 'attachment');
          default:
            break;
          }
        }
      } 
      $tplFile = $this->getHookedTemplateFileName();
      unset($values['attachment']);
      CRM_Core_Smarty::singleton()->assign('values', $values);
      // Generate PDF
      $out = CRM_Core_Smarty::singleton()->fetch($tplFile);
      $files[] = self::generatePDF($values, $out, $fileArray);
    }
    $zip = $config->customFileUploadDir . '/Grants_' . date('YmdHis') . '.zip';
    $export = new CRM_Financial_BAO_ExportFormat();
    $export->createZip($files, $zip, TRUE);
    // Initiate Download
    if (file_exists($zip)) {
      header('Content-Type: application/zip');
      header('Content-Disposition: attachment; filename=' . CRM_Utils_File::cleanFileName(basename($zip)));
      header('Content-Length: ' . filesize($zip));
      ob_clean();
      flush();
      readfile($config->customFileUploadDir . CRM_Utils_File::cleanFileName(basename($zip)));
      unlink($zip); //delete the zip to avoid clutter.
      CRM_Utils_System::civiExit();
    }
  } 
  /**
   * @return string
   */
  function getHookedTemplateFileName() {
    return 'PrintGrantPDF/GrantPDF.tpl';
  }

  function generatePDF($values, $html, $fileArray) {
    global $base_url;
    require_once("packages/dompdf/dompdf_config.inc.php");
    spl_autoload_register('DOMPDF_autoload');
    $fileName = 'Grant_'.$values['contact_id'].'_'.$values['grant_id'].'.pdf';
    $config = CRM_Core_Config::singleton();
    $filePath = $config->customFileUploadDir . $fileName;
    
    $dompdf = new DOMPDF();
    
    $dompdf->load_html($html);
    $dompdf->render();
    
    file_put_contents($filePath, $dompdf->output());
    
    // Merge attachments and files attached to custom fields
    if (!empty($fileArray)) {
      array_unshift($fileArray, $filePath);
      $name = 'Grants_'.$values['contact_id'].'_'.$values['grant_id'].'.pdf';
      $pdf = new CRM_PDFMerger_PDFMerger;
      $pdfs = '$pdf';
      foreach($fileArray as $file) {
        $pdfs .= '->addPDF("'.$file.'", "all")';
      }
      $pdfs .= '->merge("file", "'.$config->customFileUploadDir.$name.'");';
      eval($pdfs);
      $filePath = $config->customFileUploadDir . $name;
    }
    return $filePath;
  }

  function convertXLStoPDF($xls, $context) {
    global $civicrm_root;
    $config = CRM_Core_Config::singleton();
    require_once ('packages/PHPExcel.php');
    if ($context == 'custom') {
      $fileName = $xls->uri;
      $filePath = $config->customFileUploadDir.$xls->uri;
    }
    else {
      $fileName = $xls['fileName'];
      $filePath = $xls['fullPath'];
    }

    $rendererName = PHPExcel_Settings::PDF_RENDERER_DOMPDF;
    $rendererLibraryPath = $civicrm_root . '/packages/dompdf';
    if (!PHPExcel_Settings::setPdfRenderer($rendererName,$rendererLibraryPath)) {
      CRM_Core_Error::fatal(ts('NOTICE: Please set the $rendererName and $rendererLibraryPath values' .
                               '<br />' .
                               'at the top of this script as appropriate for your directory structure'));
    }
    $outputDirPath  = $config->customFileUploadDir;
    $objPHPexcel = PHPExcel_IOFactory::load($filePath);
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPexcel, 'Excel5'); 
    $objPHPexcel->addCellXf(new PHPExcel_Style);
    $objPHPexcel->addCellStyleXf(new PHPExcel_Style);
    $objPHPexcel->getDefaultStyle()->getFont()
      ->setName('Arila')
      ->setSize(10);
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPexcel, 'PDF');
    $objWriter->setPreCalculateFormulas(false);
    $objWriter->save($outputDirPath . str_replace('.xls', '.pdf', $fileName));
    return $outputDirPath . str_replace('.xls', '.pdf', $fileName);
  }
}