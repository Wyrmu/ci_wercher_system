<div class="modal <?php if (!isset($_GET['Year'])) {echo 'fade';} ?>" id="GraphChartModal">
	<div class="modal-dialog modal-xxl">
		<div class="modal-content">

		<!-- Modal Header -->
		<div class="modal-header">
			<form method="GET">
				<h4 class="modal-title"><i class="fas fa-calendar-week fa-fw"></i> Year <input type="number" id="GraphYear" name="Year" class="form-group px-2" value="<?php if (isset($_GET['Year'])) { echo $SelectedYear; } else { echo $CurrentYear; } ?>" style="border-radius: 6px; width: 100px;"></h4>
			</form>
			<div class="text-right">
				<button type="button" class="close d-none d-sm-block" data-dismiss="modal">&times;</button>
			</div>
		</div>

		<!-- Modal body -->
		<div class="modal-body">
			<div class="row rcontent w-85 ml-auto mr-auto mt-2">
				<canvas id="GraphChartModal_Chart" class="w-100" width="800" height="250"></canvas>
			</div>
			<div class="row">
				<?php for ($i = 1; $i < 13; $i++): 
						if ($i < 10) {
							$i = '0' . $i;
						} ?>
					<div class="col-md-12 col-lg-3 mb-4">
						<div class="card-container-lite">
							<?php
								$SelectMonth = $this->Model_Selects->GetMonthlyTotalSpecificMonth($SelectedYear, $i);
								foreach ($SelectMonth->result_array() as $row) {
									$Month = $row['Month'];
									$Count = $row['Total'];
								}
							?>
							<a href="Dashboard?Year=<?php echo $SelectedYear; ?>&Month=<?php echo $Month; ?>#ByMonth">
							<div class="card-headers-lite clearfix <?php if ($Count > 0) { echo 'card-headers-primary'; } else { echo 'card-headers-info'; } ?>">
								<span class="float-left card-month-text">
									<?php 
										$MonthName = DateTime::createFromFormat('!m', $i);
										echo $MonthName->format('F');
									?>
								</span>
							</div>
							<div class="card-bodys clearfix">
								<a class="dc-links float-left" href="Dashboard?Year=<?php echo $SelectedYear; ?>&Month=<?php echo $Month; ?>#ByMonth"><i class="fas fa-angle-right fa-fw" <?php if ($Count <= 0) { echo 'style="color: rgba(75, 75, 75, 0.75)"'; } ?>></i> <span <?php if ($Count <= 0) { echo 'style="color: rgba(75, 75, 75, 0.75)"'; } ?>>
								<?php echo $Count; ?> <?php if ($Count == 1) { echo 'person'; } else { echo 'people'; } ?> applied </span></a>
							</div>
							</a>
						</div>
					</div>
				<?php endfor; ?>
			</div>
			<?php if (isset($_GET['Year']) && isset($_GET['Month'])): ?>
			<hr>
			<div id="ByMonth" class="row">
				<?php
					$GetApplicantsByMonth = $this->Model_Selects->GetApplicantsByMonth($SelectedYear, $_GET['Month']);
					foreach ($GetApplicantsByMonth->result_array() as $row) {
						echo $row['FirstName'] . '<br>';
					}
				?>
			</div>
			<?php endif; ?>
		</div>

		<!-- Modal footer -->
		<div class="modal-footer">
			<button type="button" class="btn btn-danger ml-auto" data-dismiss="modal">Close</button>
		</div>

		</div>
	</div>
</div>