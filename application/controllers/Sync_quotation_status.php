<?php
class Sync_quotation_status extends CI_Controller
{
  public $mc;
  public $limit = 100;

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
        //--- -1 = Cancle, 2 = Open, 4 = Closed
        $arr = array(
          'status' => $ds->DocStatus == E ? -1 : ($ds->DocStatus == 'O' ? 2 :$ds->DocStatus == 'C' ? 4 :)
        );

        if(!$this->update($ds->WEB_ORDER, $arr))
        {
          $arr = array(
            'F_Web' => 'N',
            'F_Message' => "Update failed"
          );
        }
        else
        {
          $arr = array(
            'F_Web' => 'Y',
            'F_Message' => NULL
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
    ->select('DocEntry, WEB_ORDER, DocStatus')
    ->where('F_Web !=', 'Y')
    ->limit($this->limit)
    ->get('OQUT_STATUS_UPDATE');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  private function update($code, array $ds = array())
  {
    return $this->db->where('code', $code)->update('quotation', $ds);
  }


  private function update_temp($docEntry, array $ds = array())
  {
    return $this->mc->where('DocEntry', $docEntry)->update('OQUT_STATUS_UPDATE', $ds);
  }




} //--- end class

 ?>
