<?php

function createComp($comp, $heroes){
	$image = new Imagick();
	$draw = new ImagickDraw();
	$bgpixel = new ImagickPixel("black");
	$image->newImage(850, 250, $bgpixel);
	$image->setImageFormat("png");
	
	$draw->setFont("./assets/bignoodletoo.ttf");
	$draw->setFontStyle(Imagick::STYLE_ITALIC);
	$draw->setFontSize(80);
	$draw->setFillColor("white");
	
	$image->annotateImage($draw, 20, 80, 0, $comp["name"]);
	
	$x = 20;
	$y = 100;
	
	foreach($comp["comp"] as $hero){
		
		if(gettype($hero) == "array"){
			switch(count($hero)){
				case 2:
					$i = 0;
					foreach($hero as $subhero){
						$i++;
						$heroes[$subhero]->resizeImage(90, 90, Imagick::FILTER_LANCZOS, 1);
						if($i == 1){
							$image->compositeImage($heroes[$subhero], Imagick::COMPOSITE_DEFAULT, $x + 45, $y + 40);
						}else{
							$image->compositeImage($heroes[$subhero], Imagick::COMPOSITE_DEFAULT, $x + 5, $y);
						}
					}
					break;
				case 3:
					$i = 0;
					foreach($hero as $subhero){
						$i++;
						$heroes[$subhero]->resizeImage(90, 90, Imagick::FILTER_LANCZOS, 1);
						if($i == 1){
							$image->compositeImage($heroes[$subhero], Imagick::COMPOSITE_DEFAULT, $x + 50, $y + 40);
						}else if($i == 2){
							$image->compositeImage($heroes[$subhero], Imagick::COMPOSITE_DEFAULT, $x, $y + 40);
						}else{
							$image->compositeImage($heroes[$subhero], Imagick::COMPOSITE_DEFAULT, $x + 25, $y - 5);
						}
					}
					break;
				case 4:
					$i = 0;
					foreach($hero as $subhero){
						$i++;
						$heroes[$subhero]->resizeImage(80, 80, Imagick::FILTER_LANCZOS, 1);
						if($i == 1){
							$image->compositeImage($heroes[$subhero], Imagick::COMPOSITE_DEFAULT, $x + 35, $y + 50);
						}else if($i == 2){
							$image->compositeImage($heroes[$subhero], Imagick::COMPOSITE_DEFAULT, $x - 10, $y + 50);
						}else if($i == 3){
							$image->compositeImage($heroes[$subhero], Imagick::COMPOSITE_DEFAULT, $x + 15, $y + 10);
						}else{
							$image->compositeImage($heroes[$subhero], Imagick::COMPOSITE_DEFAULT, $x + 65, $y + 10);
						}
					}
					break;
			}
			//echo count($hero);
		}else{
			$image->compositeImage($heroes[$hero], Imagick::COMPOSITE_DEFAULT, $x, $y);
		}
		$x += 135.5;
	}
	
	return $image;
}

function processPost(){
	$allComps = array();	
	$compAmount = sizeof($_POST) / 7;	
	
	for($i = 1; $i <= $compAmount; $i++){
		$comp = array();
		for($j = 1; $j <= 6; $j++){
			if(sizeof($_POST["comp{$i}-hero{$j}"]) == 1){
				array_push($comp, $_POST["comp{$i}-hero{$j}"][0]);
			}else{
				$multihero = array();
				foreach($_POST["comp{$i}-hero{$j}"] as $hero){
					array_push($multihero, $hero);
				}
				array_push($comp, $multihero);
			}
		}
		
		$finalComp = array(
			"name" => $_POST["comp{$i}-name"],
			"comp" => $comp
		);
		array_push($allComps, $finalComp);
	}
	return $allComps;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	
	// Load heroes
	$heroes = array();

	foreach(array_diff(scandir("./assets/heroes"), array('..', '.')) as $file){
		$name = str_replace(".png", "", $file);
		$heroes[$name] = new Imagick();
		$heroes[$name]->readImage("./assets/heroes/{$file}");
		$heroes[$name]->resizeImage($heroes[$name]->getImageWidth() / 2, $heroes[$name]->getImageWidth() / 2, Imagick::FILTER_LANCZOS, 1);
	}	

	/* TEST COMPS
	$comp1 = array(
		"name" => "Goats",
		"comp" => array(
			"Reinhardt",
			"Zarya",
			"DVa",
			"Brigitte",
			array(
				"Zenyatta",
				"Ana",
				"Moira"
			),
			"Mei"
		)
	);
	
	echo "<br /><br /><br />";
	print_r($comp1);

	$comp2 = array(
		"name" => "Goats",
		"comp" => array(
			array(
				"Reinhardt",
				"Winston"
			),
			"Zarya",
			"DVa",
			"Brigitte",
			array(
				"Zenyatta",
				"Ana",
				"Moira",
				"Lucio"
			),
			"Mei"
		)
	);
	
	$comps = array($comp1, $comp2);
	*/
	
	$comps = processPost();

	$image = new Imagick();
	$draw = new ImagickDraw();
	$bgpixel = new ImagickPixel("black");

	$width = 850;
	$height = (count($comps) * 250) + 150;

	$image->newImage($width, $height, $bgpixel);
	$image->setImageFormat("png");

	$draw->setFillColor("white");

	$draw->setFont("./assets/bignoodletoo.ttf");
	$draw->setFontStyle(Imagick::STYLE_ITALIC);
	$draw->setFontSize(100);

	$image->annotateImage($draw, 20, 100, 0, "Composition Cheat Sheet");

	$draw->setFontSize(80);

	$y = 150;
	foreach($comps as $comp){
		$comp = createComp($comp, $heroes);
		$image->compositeImage($comp, Imagick::COMPOSITE_DEFAULT, 20, $y);
		$y += 250;
	}
	
	header("Content-Type: image/png");
	//echo $comp;
	echo $image;

}else{
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.1.3/slate/bootstrap.min.css">

    <title>Composition Cheat Sheet Builder</title>
  </head>
  <body>
  <form action="./index.php" method="post">
		<div class="container">
			<div class="container-fluid">
				<h1>Composition Cheat Sheet Builder</h1>
				<div class="row">
					<br />
				</div>
				<div id="row-anchor"></div>
				<div class="row">
					<div class='col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 mt-4'>
						<div class="btn-group-vertical">
							<a href='#' id='addComp' onclick='addComp()' class='nav-link'>Add composition</a>
							<input type="submit" value="Generate" class="btn btn-primary btn-sm">
						</div>
					</div>
				</div>
			</div>
		</div>
				
		<template id="comp-template">
			<div class="row">
				<div class='col-md-auto'>
					<div class="btn-group-vertical">
						<input type="text" class="form-control" id="comp[i]-name" name="comp[i]-name" value="Composition [i]">
					</div>
				</div>
			</div>
			<div id="comp[i]" class="row">
				<div id="comp[i]-hero1">
				</div>
				<div id="comp[i]-hero2">
				</div>
				<div id="comp[i]-hero3">
				</div>
				<div id="comp[i]-hero4">
				</div>
				<div id="comp[i]-hero5">
				</div>
				<div id="comp[i]-hero6">
				</div>
			</div>
			<div class="row">
				<br />
			</div>
		</template>
		
		<template id="comp-column-template">
			<div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 mt-4">
				<div class="card">
					<img src="./assets/heroes/Ana.png" id="comp[i]-img[j]" width="100%"><br>
						<div id="hero-select"></div>
					<a href="#" id="comp[i]-addHero[j]" onclick="addHero([i], [j])" class="nav-link">+ Add hero</a>
				</div>
			</div>
		</template>
		
		<template id="hero-select-template">
			<select name="comp[i]-hero[j][]" id="comp[i]-hero[j]" onchange="showHero([i], [j])" class="form-control">
				<option value="Ana" selected>Ana</option>
				<option value="Bastion">Bastion</option>
				<option value="Brigitte">Brigitte</option>
				<option value="DVa">DVa</option>
				<option value="Doomfist">Doomfist</option>
				<option value="Genji">Genji</option>
				<option value="Hanzo">Hanzo</option>
				<option value="Junkrat">Junkrat</option>
				<option value="Lucio">Lucio</option>
				<option value="McCree">McCree</option>
				<option value="Mei">Mei</option>
				<option value="Mercy">Mercy</option>
				<option value="Moira">Moira</option>
				<option value="Orisa">Orisa</option>
				<option value="Pharah">Pharah</option>
				<option value="Reaper">Reaper</option>
				<option value="Reinhardt">Reinhardt</option>
				<option value="Roadhog">Roadhog</option>
				<option value="Soldier 76">Soldier 76</option>
				<option value="Sombra">Sombra</option>
				<option value="Symmetra">Symmetra</option>
				<option value="Torbjorn">Torbjorn</option>
				<option value="Tracer">Tracer</option>
				<option value="Widowmaker">Widowmaker</option>
				<option value="Winston">Winston</option>
				<option value="Wrecking Ball">Wrecking Ball</option>
				<option value="Zarya">Zarya</option>
				<option value="Zenyatta">Zenyatta</option>
			</select>
		</template>
		
		<!-- jQuery first, then Popper.js, then Bootstrap JS -->
		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	
		
		<!-- Optional JavaScript -->
		<script>
			function showHero(i, j) {
				hero = $("#comp" + i + "-hero" + j).children("option:selected").val();
				$("#comp" + i + "-img" + j).attr("src", "./assets/heroes/" + hero + ".png");
			}
			
			function addHero(i, j) {
				//$("#comp" + i + "-hero" + j).find("#hero-select").replaceWith($("#hero-select-template").html().replace("[i]", i).replace("[j]", j));
				$("#comp" + i + "-addHero" + j).before($("#hero-select-template").html().replace(/\[i\]/g, i).replace(/\[j\]/g, j));
			}
			
			function addComp() {
				compAmount++;				
				$("#row-anchor").before($("#comp-template").html().replace(/\[i\]/g, compAmount));
				j = 0;
				$("#comp" + compAmount).find("div").each(function () {
					j++;
					$(this).replaceWith($("#comp-column-template").html().replace(/\[i\]/g, compAmount).replace(/\[j\]/g, j));
				});
				j = 0;
				$("#comp" + compAmount).find("div #hero-select").each(function () {
					j++;
					$(this).replaceWith($("#hero-select-template").html().replace(/\[i\]/g, compAmount).replace(/\[j\]/g, j));
				});
			}
			
			compAmount = 0;
			addComp();
		</script>
	
	</form>
  </body>
</html>

<?php
}
?>