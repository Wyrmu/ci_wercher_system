<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_Selects extends CI_Model {

	public function GetEmployee()
	{
		$SQL = "SELECT * FROM applicants WHERE Status = 'Active'";
		$result = $this->db->query($SQL);
		return $result;
	}
	public function getApplicant()
	{
		$SQL = "SELECT * FROM applicants WHERE Status = 'Applicant'";
		$result = $this->db->query($SQL);
		return $result;
	}
	public function CheckEmployee($ApplicantID)
	{
		$SQL = "SELECT * FROM applicants WHERE ApplicantID = ?";
		$result = $this->db->query($SQL,$ApplicantID);
		return $result;
	}
	public function GetEmployeeDetails($id)
	{
		$SQL = "SELECT * FROM applicants WHERE ApplicantNo = ?";
		$result = $this->db->query($SQL,$id);
		return $result;
	}
	public function GetEmployeeAcadhis()
	{
		$SQL = "SELECT * FROM acad_history";
		$result = $this->db->query($SQL);
		return $result;
	}
	public function GetAdmin()
	{
		$SQL = "SELECT * FROM admin";
		$result = $this->db->query($SQL);
		return $result;
	}
	public function CheckAdminID($AdminID)
	{
		$SQL = "SELECT * FROM admin WHERE AdminID = ?";
		$result = $this->db->query($SQL,$AdminID);
		return $result;
	}
	public function GetApplicants()
	{
		$SQL = "SELECT * FROM applicants";
		$result = $this->db->query($SQL);
		return $result;
	}
	public function GetApplicantSkills()
	{
		$result =  $this->db->query("SELECT PositionDesired, COUNT(*) as count FROM applicants GROUP BY PositionDesired");
		return $result;
	}
}
