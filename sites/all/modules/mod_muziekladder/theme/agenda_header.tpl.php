		<div class="row controls">		
			<div class="span6">
				<nav class="prevnextlinks">
					<div class="prevnextlinks btn-group">
						<button class="btn btn-inverse prevday" data-href="<?php echo $prevlink ?>"><a href="<?php echo $prevlink ?>"><i class="icon-backward icon-white"></i> Vorige dag</a></button>
						<button class="btn btn-inverse today" data-href="/muziek/"><a href="/muziek/"><i class="icon-home icon-white"></i> Vandaag</a></button>						
						<button class="btn btn-inverse nextday" data-href="<?php echo $nextlink ?>"><a href="<?php echo $nextlink ?>">Volgende dag <i class="icon-forward icon-white"></i></a></button>
					</div>	
				</nav>
			</div>		
		
			<div class="span3 dateselectContainer dayselectContainer">
				<div class="form-inline">
					<label>dag:</label>
					<select class="dayselect"></select>
				</div>
			</div>
			<div class="span3 dateselectContainer monthselectContainer">
				<div class="form-inline">
					<label>maand:</label>
					<select class="monthselect"></select>
				</div>
			</div>	
	
		</div>

