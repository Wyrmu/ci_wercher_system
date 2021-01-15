<?php

$T_Header;
require 'vendor/autoload.php';
use Carbon\Carbon;

$date = new DateTime($createdDate);
$day = $date->format('Y-m-d');
$day = DateTime::createFromFormat('Y-m-d', $day)->format('F d, Y');
$hours = $date->format('h:i:s A');
$elapsed = Carbon::parse($createdDate);

?>
<body>
	<div class="wrapper wercher-background-lowpoly">
		<?php $this->load->view('_template/users/u_sidebar'); ?>
		<div id="content" class="ncontent">
			<div class="container-fluid">
				<?php $this->load->view('_template/users/u_notifications'); ?>
				<div class="row wercher-tablelist-container rcontent PrintOutTable">
					<div class="col-sm-4 col-md-4 mb-2">
						<h4>
							<i class="fas fa-table"></i> SSS Table
						</h4>
					</div>
					<div class="col-8 col-sm-8 col-md-8 text-right">
						<button href="#" class="btn btn-info" data-toggle="modal" data-target="#AddSSSdata">
							<i class="fas fa-plus"></i> New Line
						</button>
						<a href="#" class="btn btn-primary" data-toggle="modal" data-target="#add_UserAdmin" data-backdrop="static" data-keyboard="false">
							<i class="fas fa-redo"></i> New Batch
						</a>
						<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ExportModal"><i class="fas fa-download"></i> Export</button>
					</div>
					<div class="col-4 col-sm-4 col-md-4 PrintPageName PrintOut">
						<h4 class="sss-datecreated">
							<a href="#">Batch #<?php echo $latestBatch; ?></a> | Created on <span data-toggle="tooltip" data-placement="top" data-html="true" title="<?php echo $elapsed->diffForHumans(); ?>"><?php echo $day . ' at ' . $hours; ?></span>
						</h4>
					</div>
					<div class="col-sm-12 col-mb-12 mt-2">
						<div class="table-responsive">
							<table id="SalaryTable" class="table table-condensed">
								<thead>
									<th>Range</th>
									<th>&nbsp;</th>
									<th>&nbsp;</th>
									<th>Contribution ER</th>
									<th>Contribution EE</th>
									<th>Contribution EC</th>
									<th>Total</th>
									<th>Total with EC</th>
									<th style="width: 75px;">Action</th>
								</thead>
								<tbody>
									<form method="POST" action="UpdateSSSField">
									<?php foreach ($GetSSSBatchRows->result_array() as $row) { ?>
										<?php if(isset($_GET['row']) && ($_GET['row'] == $row['id'])): ?>
											<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
										<?php endif; ?>
										<tr id="<?php echo $row['id']; ?>" class="sss-row">
											<td>
												<?php if(isset($_GET['row']) && ($_GET['row'] == $row['id'])): ?>
													<input class="form-control w-75" type="number" name="f_range" step=".01" value="<?php echo $row['f_range']; ?>">
												<?php else: ?>
													<?php print $row['f_range']; ?>
												<?php endif; ?>
											</td>
											<td>
												-
											</td>
											<td>
												<?php if(isset($_GET['row']) && ($_GET['row'] == $row['id'])): ?>
													<input class="form-control w-75" type="number" name="t_range" step=".01" value="<?php echo $row['t_range']; ?>">
												<?php else: ?>
													<?php print $row['t_range']; ?>
												<?php endif; ?>
											</td>
											<td>
												<?php if(isset($_GET['row']) && ($_GET['row'] == $row['id'])): ?>
													<input class="form-control w-75" type="number" name="contribution_er" step=".01" value="<?php echo $row['contribution_er']; ?>">
												<?php else: ?>
													<?php print $row['contribution_er']; ?>
												<?php endif; ?>
											</td>
											<td>
												<?php if(isset($_GET['row']) && ($_GET['row'] == $row['id'])): ?>
													<input class="form-control w-75" type="number" name="contribution_ee" step=".01" value="<?php echo $row['contribution_ee']; ?>">
												<?php else: ?>
													<?php print $row['contribution_ee']; ?>
												<?php endif; ?>
											</td>
											<td>
												<?php if(isset($_GET['row']) && ($_GET['row'] == $row['id'])): ?>
													<input class="form-control w-75" type="number" name="contribution_ec" step=".01" value="<?php echo $row['contribution_ec']; ?>">
												<?php else: ?>
													<?php print $row['contribution_ec']; ?>
												<?php endif; ?>
											</td>
											<td>
												<?php echo $row['total']; ?>
											</td>
											<td>
												<?php echo $row['total_with_ec']; ?>
											</td>
											<td width="140">
												<?php if(isset($_GET['row']) && ($_GET['row'] == $row['id'])): ?>
													<button class="btn btn-success btn-sm w-100 mb-1" type="submit"><i class="fas fa-check fa-fw"></i> Update</button>
													<a class="btn btn-secondary btn-sm w-100 mb-1" href="<?php echo base_url() ?>SSS_Table"><i class="fas fa-times fa-fw"></i> Cancel</a>
												<?php elseif(isset($_GET['delete']) && ($_GET['delete'] == $row['id'])): ?>
													<a class="btn btn-danger btn-sm w-100 mb-1" href="<?php echo base_url() ?>DeleteSSSTableRow?row=<?php echo $row['id']; ?>"><i class="fas fa-trash-alt fa-fw"></i> Confirm</button>
													<a class="btn btn-secondary btn-sm w-100 mb-1" href="<?php echo base_url() ?>SSS_Table"><i class="fas fa-times fa-fw"></i> Cancel</a>
												<?php else: ?>
													<a class="btn btn-info btn-sm w-100 mb-1" href="<?php echo base_url() ?>SSS_Table?row=<?php echo $row['id']; ?>"><i class="fas fa-edit fa-fw"></i> Update</a>
													<a class="btn btn-danger btn-sm w-100 mb-1" href="<?php echo base_url() ?>SSS_Table?delete=<?php echo $row['id']; ?>"><i class="fas fa-trash-alt fa-fw"></i> Delete</a>
												<?php endif; ?>
											</td>
										</tr>
									<?php } ?>
									</form>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Modal -->
	<div class="modal fade" id="AddSSSdata" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<?php echo form_open(base_url().'AddthisSss','method="post"'); ?>
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Add to SSS table</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="text-center">
						<h5>
							Range of Compensations
						</h5>
					</div>
					<div class="form-row text-center">
						<div class="form-group m-auto col-sm-6 col-md-6">
							<label>From</label>
							<input class="form-control text-center" type="number" step=".01" name="f_range">
						</div>
						<div class="form-group m-auto col-sm-6 col-md-6">
							<label>To</label>
							<input class="form-control text-center" type="number" step=".01" name="t_range">
						</div>
					</div>
					<div class="text-center mt-4">
						<h5> 
							Contributions
						</h5>
					</div>
					<div class="form-row text-center">
						<div class="form-group m-auto col-sm-4 col-md-4">
							<label>ER</label>
							<input class="form-control text-center" type="number" step=".01" name="contribution_er">
						</div>
						<div class="form-group m-auto col-sm-4 col-md-4">
							<label>EE</label>
							<input class="form-control text-center" type="number" step=".01" name="contribution_ee">
						</div>
						<div class="form-group m-auto col-sm-4 col-md-4">
							<label>EC</label>
							<input class="form-control text-center" type="number" step=".01" name="contribution_ec">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Add</button>
				</div>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</body>
<?php $this->load->view('_template/users/u_scripts'); ?>
<script type="text/javascript">
	$(document).ready(function () {
		$('[data-toggle="tooltip"]').tooltip();
		$(".nav-item a[href*='Payroll']").addClass("nactive");
		$('#TaxTable').hide();
	});
</script>
</html>