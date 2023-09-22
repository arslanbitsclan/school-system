<?php $currency_symbol = $global_config['currency_symbol']; ?>
<section class="panel">
	<div class="tabs-custom">

		<div class="tab-content">
			<div id="invoice" class="tab-pane <?=empty($this->session->flashdata('pay_tab')) ? 'active' : ''; ?>">
				<div id="invoice_print">
					<div class="invoice">
						<header class="clearfix">
							<div class="row">
								<div class="col-xs-6">
									<div class="ib">
										<img src="<?=base_url('uploads/app_image/printing-logo.png')?>" alt="AanttechCoder Img" />
									</div>
								</div>
								<div class="col-md-6 text-right">
									<h4 class="mt-none mb-none text-dark">Voucher No #<?=$voucher->voucher_no?></h4>
									<p class="mb-none">
										<span class="text-dark"><?=translate('voucher_month')?> : </span>
										<span class="value"><?=get_voucher_month($voucher->fee_month)?></span>
									</p>

									<p class="mb-none">
										<span class="text-dark"><?=translate('due_date')?> : </span>
										<span class="value"><?=_d($voucher->due_date)?></span>
									</p>

									<p class="mb-none">
										<span class="text-dark"><?=translate('valid_date')?> : </span>
										<span class="value"><?=_d($voucher->valid_date)?></span>
									</p>

									<p class="mb-none">
										<span class="text-dark"><?=translate('status')?> : </span>
										<?php
											$labelmode = '';
											if($voucher->status == 0) {
												$status = translate('unpaid');
												$labelmode = 'label-danger-custom';
											} elseif($voucher->status == 2) {
												$status = translate('partly_paid');
												$labelmode = 'label-info-custom';
											} elseif($voucher->status == 1) {
												$status = translate('total_paid');
												$labelmode = 'label-success-custom';
											}
											echo "<span class='value label " . $labelmode . " '>" . $status . "</span>";
										?>
									</p>
								</div>
							</div>
						</header>
						<div class="bill-info">
							<div class="row">
								<div class="col-xs-6">
									<div class="bill-data">
										<p class="h5 mb-xs text-dark text-weight-semibold">Invoice To :</p>
										<address>
											<?php 
											echo $basic['first_name'] . ' ' . $basic['last_name'] . '<br>';
											echo $basic['student_address'] . '<br>';
											echo translate('class') . ' : ' . $basic['class_name'] . '<br>';
											echo translate('email') . ' : ' . $basic['student_email']; 
											?>
										</address>
									</div>
								</div>
								<div class="col-xs-6">
									<div class="bill-data text-right">
										<p class="h5 mb-xs text-dark text-weight-semibold">Academic :</p>
										<address>
											<?php 
											echo $basic['school_name'] . "<br/>";
											echo $basic['school_address'] . "<br/>";
											echo $basic['school_mobileno'] . "<br/>";
											echo $basic['school_email'] . "<br/>";
											?>
										</address>
									</div>
								</div>
							</div>
						</div>

						<div class="table-responsive br-none">
							<table class="table invoice-items table-hover mb-none">
								<thead>
									<tr class="text-dark">
										<th id="cell-id" class="text-weight-semibold">#</th>
										<th id="cell-item" class="text-weight-semibold"><?=translate("fees_type")?></th>
										<th id="cell-price" class="text-weight-semibold"><?=translate("amount")?></th>
										<th id="cell-price" class="text-weight-semibold"><?=translate("discount")?></th>
										<th id="cell-price" class="text-weight-semibold"><?=translate("fine")?></th>
										<th id="cell-total" class="text-center text-weight-semibold"><?=translate("balance")?></th>
									</tr>
								</thead>
								<tbody>
									<?php
										$count = 1;
										$total_fine = 0;
										$total_discount = 0;
										$total_paid = 0;
										$total_balance = 0;
										$total_amount = 0;
										$typeData = array('' => translate('select'));
										$previous_open_balance = previous_open_balance($voucher->student_id);

										

										// get total paid amount of this voucher

										$total_paid = get_paid_amount_voucher($voucher->id);

										$allocations = $this->fees_model->getVoucherFeeDetails($voucher->id);

										if($voucher->carry_balance == 1){
											$previous_balance = previous_balance($voucher->student_id);
										}else{
											$previous_balance = 0;
										}

										foreach ($allocations as $row) {

											$type_discount = 0;
											$deposit = $this->fees_model->getStudentFeeDeposit($row['allocation_id'], $row['fee_type_id']);
								
											$check_month = check_voucher_month($voucher->id,$row['fee_type_id']);


											$discount_info = get_fee_type_discount($student_id,$row['fee_type_id']);

											if(!empty($discount_info)){
												if(!empty($discount_info->discount)){
													$type_discount = number_format(($discount_info->discount*$check_month), 2, '.', '');
													
												}else{
													$total_discount += 0;
													$type_discount = number_format(0, 2, '.', '');
												}
											}

											$type_fine = number_format(0, 2, '.', '');

											$fine = getBalanceByType($row['allocation_id'], $row['fee_type_id'],$voucher->voucher_no);

											if($fine > 0){
												$type_fine =  number_format($fine, 2, '.', '');
											}

											//$balance = ($row['amount']*$check_month) + $type_fine;

											$balance = ($row['amount']*$check_month - $type_discount) + $type_fine;


											
											$total_discount += $type_discount;
											$total_fine += $type_fine;

					
											$total_balance += $balance;
											$total_amount += $row['amount']*$check_month;

											if ($balance != 0) {
											 	$typeData[$row['allocation_id'] . "|" . $row['fee_type_id']] = $row['fee_head'];
											}
										?>
									<tr>
										<td><?php echo $count++;?></td>
										<td class="text-weight-semibold text-dark"><?=$row['fee_head']?></td>
										<td><?php echo $currency_symbol . $row['amount'];?> </td>
										<td><?php echo $currency_symbol . $type_discount;?></td>
										<td><?php echo $currency_symbol . $type_fine;?></td>
										
										<td class="text-center"><?php echo $currency_symbol . number_format($balance, 2, '.', '');?></td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
						<div class="invoice-summary text-right mt-lg">
							<div class="row">
								<div class="col-lg-5 pull-right">
									<ul class="amounts">
										<?php 
											if(!empty($payment)){

												$total_amount = $payment->amount;
												$total_paid = $payment->total_paid;
												$total_discount = $payment->total_discount;
												$previous_balance = $payment->previous_balance;
												$previous_open_balance = $payment->previous_opening_balance;

											}
										?>
										<li><strong><?=translate('grand_total')?> :</strong> <?=$currency_symbol . number_format($total_amount, 2, '.', ''); ?></li>
										<li><strong><?=translate('paid')?> :</strong> <?=$currency_symbol . number_format($total_paid, 2, '.', ''); ?></li>
										<li><strong><?=translate('discount')?> :</strong> <?=$currency_symbol . number_format($total_discount, 2, '.', ''); ?></li>

										<?php
										if($voucher->carry_balance == 1){
											$previous_balance = previous_balance($voucher->student_id);

											if($previous_balance > 0){ ?>

												<li>
											<strong><?=translate('previous_balance')?> : </strong> 
											<?php
											$f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
											echo $currency_symbol . number_format($previous_balance, 2, '.', '');
											?>
											</li> <?php
											} 

										}
										?>

										<?php
										if($previous_open_balance > 0){ ?>
											<li>
											<strong><?=translate('previous_open_balance')?> : </strong> 
											<?php
											$f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
											echo $currency_symbol . number_format($previous_open_balance, 2, '.', '');
											?>
											</li> <?php
											

										}
										?>

										<li><strong><?=translate('fine')?> :</strong> <?=$currency_symbol . number_format($total_fine, 2, '.', ''); ?></li>
										<?php if ($total_balance != 0): ?>
										<li>
											<strong><?=translate('balance')?> : </strong> 
											<?php
											$f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
											echo $currency_symbol . number_format($total_balance-$total_paid+$previous_balance+$previous_open_balance, 2, '.', '') . ' </br>( ' . ucwords($f->format($total_balance-$total_paid+$previous_balance+$previous_open_balance)) . ' )' ;
											?>
										</li>
										<?php else: ?>
										<li>
											<strong><?=translate('total_paid')?> : </strong> 
											<?php
											$f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
											echo $currency_symbol . number_format(($total_paid + $total_fine+$previous_balance+$previous_open_balance), 2, '.', '') . ' </br>( ' . ucwords($f->format(($total_paid + $total_fine+$previous_balance+$previous_open_balance))) . ' )' ;
											?>
										</li>
										<?php endif; ?>


										<?php if($total_paid  ==  0){ ?>
										<li>
											
											<?php echo form_open('fees/collect_fee', array('class' => 'form-horizontal frm-submit')); ?>
											<strong><?=translate('paid_amount')?> : </strong> 
											<input type="number" class="form-control" style="display: inline;width: 40%;" name="paid_amount" value="<?=$total_balance-$total_paid+$previous_balance+$previous_open_balance?>" min="0" max="<?=$total_balance-$total_paid+$previous_balance+$previous_open_balance?>">

											<input type='hidden' name='voucher_id' value='<?=$voucher->id?>' />
											<input type='hidden' name='student_id' value='<?=$voucher->student_id?>' />
											<input type='hidden' name='previous_open_balance' value='<?=$previous_open_balance?>' />

											<input type='hidden' name='total_discount' value='<?=$total_discount?>' />
											<input type='hidden' name='total_fine' value='<?=$total_fine?>' />
											<input type='hidden' name='previous_balance' value='<?=$previous_balance?>' />



											<input type='hidden' name='amount' value='<?=$total_balance+$previous_balance+$previous_open_balance?>' />
											<button type="submit" class="btn btn-default ml-sm"><i class="fas fa-save"></i> </button>
										<?php echo form_close();?>
										</li>
									<?php } ?>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>


<script type="text/javascript">
    $('#fees_type').on("change", function(){
        var typeID = $(this).val();

	    $.ajax({
	        url: base_url + 'fees/getBalanceByType',
	        type: 'POST',
	        data: {
	        	'typeID': typeID
	        },
	        dataType: "json",
	        success: function (data) {
	            $('#feeAmount').val(data.balance.toFixed(2));
	            $('#fineAmount').val(data.fine.toFixed(2));
	        }
	    });
    });
</script>