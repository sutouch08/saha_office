<?php
class Sync_sale_order_status extends CI_Controller
{
  public $mc;
  public $limit = 50;

  public function __construct()
  {
    parent::__construct();
    $this->mc = $this->load->database('mc', TRUE); //--- Temp Database
  }


  public function index()
  {
    $list = $this->getUpdateList();

    if(!empty($list))
    {
      foreach($list as $ds)
      {
        //--- O = Open, C= Closed, E = Cancled
        $arr = array(
          'SapStatus' => $ds->DocStatus,
          'DeliveryNo' => $ds->DeliveryNo,
          'InvoiceNo' => $ds->InvoiceNo
        );

        if(!$this->update($ds->WEB_ORDER, $arr))
        {
          $arr = array(
            'F_Web' => 'N',
            'F_Message' => "Update failed",
            'F_WebDate' => now()
          );
        }
        else
        {
          $arr = array(
            'F_Web' => 'Y',
            'F_Message' => NULL,
            'F_WebDate' => now()
          );
        }

        $this->update_temp($ds->DocEntry, $arr);

      } //--- endforeach
    }
  }


  private function getUpdateList()
  {
    //--- status O = open, C = Clos
    $rs = $this->mc
    ->select('DocEntry, WEB_ORDER, DeliveryNo, InvoiceNo, DocStatus')
    ->group_start()
    ->where('F_Web IS NULL', NULL, FALSE)
    ->or_where('F_Web', 'N')
    ->group_end()
    ->where('DocNum >', 0)
    ->limit($this->limit)
    ->get('ORDR_STATUS_UPDATE');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  private function update($code, array $ds = array())
  {
    return $this->db->where('code', $code)->update('sales_order', $ds);
  }


  private function update_temp($docEntry, array $ds = array())
  {
    return $this->mc->where('DocEntry', $docEntry)->update('ORDR_STATUS_UPDATE', $ds);
  }

} //--- end class

 ?>
