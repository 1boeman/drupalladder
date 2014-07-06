    <nav>
	<ul class="breadcrumb">
	  <li><a href="/muziek/">Agenda</a> <span class="divider"><i class="icon-chevron-right"></i></span></li>
	  <li class="active"><span><?php echo $title ?></span></li>
	</ul>
    </nav>
	<div class="eventfull clearfix" itemscope itemtype="http://schema.org/Event" data-imgsrc="<?php echo $img ?>">
		<a itemprop="url" href="<?php echo $link ?>" class="eventlink nodisplay">
			<span itemprop="name"><?php echo $title  ?></span>
		</a>
	
        <h1><a target="_blank" href="<?php echo $link ?>"><?php echo $title ?></a></h1>
		<h3>
			<a target="_blank" href="<?php echo $link ?>">
				<strong itemprop="startDate" class="date"><?php echo $date ?></strong>
			</a>
			
		</h3>
        <h4><a target="_blank" href="<?php echo $link ?>"><?php echo $location_title ?></a> 
            &bull; <span class="city city<?php echo $cityno ?>" data-cityno="<?php echo $cityno ?>"><?php echo $city ?></span> 
        </h4>
		<p><strong>Info</strong>: <?php echo $desc ?></p>
		<p><strong>Link</strong>: <a itemprop="url" href="<?php echo $link ?>"><?php echo $link ?></a>  </p>
		<p><i class="icon-info-sign"></i> <a itemprop="url" href="<?php echo $link ?>">Meer informatie</a></p>

		<div class="location" itemprop="location" itemscope itemtype="http://schema.org/Place">
			<h3><a href="<?php echo $location_link ?>"><span itemprop="name"><?php echo $location_title ?></span></a></h3>
			<p class="description"><?php echo $location_desc ?></p>
			<h4>Locatie	:</h4>
			<p>
				<div> <?php echo $street ?> </div>  
				<div><?php echo $streetnumber .' ' .  $streetnumberAddition ?></div> 
				<div itemprop="postalCode"><?php echo $zip ?></div>  
				<div class="city city<?php echo $cityno ?>" data-cityno="<?php echo $cityno ?>"><?php echo $city ?></div> 
			</p>
			<p><a itemprop="url" href="<?php echo $location_link ?>"><i class="icon-info-sign"></i> Meer info over <?php echo $location_title ?></a></p>
		</div>
	</div>


