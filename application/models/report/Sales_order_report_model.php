<?php
class Sales_order_report_model extends CI_Model
{
  private $tb = "ORDR";
  private $td = "RDR1";

  public function __construct()
  {
    parent::__construct();
  }

  public function get_orders(object $ds)
  {

    $qr  = "SELECT DocEntry, DocNum, CardCode, CardName, DocDate, DocDueDate, DocStatus ";
		$qr .= "FROM ORDR ";
		$qr .= "WHERE DocStatus = 'O' ";

    if($ds->date_type == 'R')
    {
      $qr .= "AND DocDueDate >= '".from_date($ds->from_date)."' ";
      $qr .= "AND DocDueDate <= '".to_date($ds->to_date)."' ";
    }
    else
    {
      $qr .= "AND DocDate >= '".from_date($ds->from_date)."' ";
      $qr .= "AND DocDate <= '".to_date($ds->to_date)."' ";
    }

		if( ! empty($ds->so_code))
		{
			$qr .= "AND DocNum Like '%{$ds->so_code}%' ";
		}

		if( ! empty($ds->customer))
		{
			$qr .= "AND (CardCode LIKE N'%{$ds->customer}%' OR CardName LIKE N'%{$ds->customer}%') ";
		}

		$qr .= "ORDER BY DocNum ASC ";

    $rs = $this->ms->query($qr);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function getOpenRows($DocEntry, $itemCode = NULL)
  {
    $qr  = "SELECT DocEntry, LineNum, LineStatus, ItemCode, Dscription, ";
    $qr .= "Quantity, OpenQty, InvQty, OpenInvQty, Price, ";
    $qr .= "UomEntry, UomEntry2, UomCode, UomCode2, unitMsr, unitMsr2 ";
    $qr .= "FROM RDR1 ";
    $qr .= "WHERE DocEntry = {$DocEntry} AND LineStatus = 'O' ";
    $qr .= "AND OpenQty > 0 ";

    if( ! empty($itemCode))
    {
      $qr .= "AND (ItemCode LIKE N'%{$itemCode}%' OR Dscription LIKE N'%{$itemCode}%') ";
    }

    $rs = $this->ms->query($qr);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

} //--- end class

 ?>
