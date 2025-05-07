<?php

	define('BORDER_WIDTH', 			12);
	define('BLEED_WIDTH', 			0);
	define('CARD_WIDTH', 			372);
	define('CARD_HEIGHT', 			519);
	define('OUTPUT_WIDTH', 			744);
	define('OUTPUT_HEIGHT', 		1038);
	define('OUTPUT_RESOLUTION', 	300);

	# pull directory
	$inputDir = __DIR__.'/input/';
	$outputDir = __DIR__.'/output/';

	function startNewImage() {
		# sizing notes: https://www.reddit.com/r/mpcproxies/comments/e9q1z7/complete_guide_to_image_sizing_for_mpc_or_other/
		$image = @imagecreatetruecolor(OUTPUT_WIDTH+BLEED_WIDTH, OUTPUT_HEIGHT+BLEED_WIDTH)
		  or die('Cannot Initialize new GD image stream');

		# set resolution
		imageresolution($image, OUTPUT_RESOLUTION);

		return $image;
	}

	echo "\033[32mRunning Images...\033[0m\n";
	$finalImage = startNewImage();

	# loop images
	$currentImage = 0;
	$currentOutput = 0;

	foreach (scandir($inputDir) as $index => $item)
	{
		if (in_array(strtolower(trim($item)), ['.', '..', '.gitkeep', '.ds_store', 'thumbs.db'])) {
			continue;
		}

		echo "\033[34mCopying {$item}...\033[0m\n";

		$fullInputPath = $inputDir.$item;
		$extension = strtolower(pathinfo($fullInputPath, PATHINFO_EXTENSION));

		if (in_array($extension, ['jpg', 'jpeg'])) {
			$newImage = imagecreatefromjpeg($fullInputPath);
		} elseif ($extension == 'webp') {
			$newImage = imagecreatefromwebp($fullInputPath);
		} elseif ($extension == 'png') {
			$newImage = imagecreatefrompng($fullInputPath);
		} else {
			echo ("CANNOT HANDLE IMAGES WITH EXTENSION: '{$extension}'. Skipping...\n");
			continue;
		}

		imageresolution($newImage, OUTPUT_RESOLUTION);
		$imageSize = getimagesize($fullInputPath);

		$cardOffset = match ($currentImage) {
			0 => [BLEED_WIDTH, 				BLEED_WIDTH],
			1 => [BLEED_WIDTH+CARD_WIDTH,	BLEED_WIDTH],
			2 => [BLEED_WIDTH, 				BLEED_WIDTH+CARD_HEIGHT],
			3 => [BLEED_WIDTH+CARD_WIDTH,	BLEED_WIDTH+CARD_HEIGHT],
		};

		imagecopyresampled(
			$finalImage, 
			$newImage, 
			$cardOffset[0], 
			$cardOffset[1], 
			0, 
			0, 
			CARD_WIDTH, 
			CARD_HEIGHT, 
			$imageSize[0],
			$imageSize[1]
		);

		if ($currentImage == 3) {
			$finalImagePath = "combined-".str_pad($currentOutput, 4, "0", STR_PAD_LEFT).".jpg";
			imagejpeg($finalImage, $outputDir.$finalImagePath, 100);
			echo "\033[33m -- WROTE {$finalImagePath}\033[0m\n";
			$finalImage = startNewImage();
			$currentImage = 0;
			$currentOutput++;
		} else {
			$currentImage++;
		}
	}

	if ($currentImage > 0) {
		$finalImagePath = "combined-".str_pad($currentOutput, 4, "0", STR_PAD_LEFT).".jpg";
		imagejpeg($finalImage, $outputDir.$finalImagePath, 100);
		echo "\033[33m -- WROTE {$finalImagePath}\033[0m\n";
	}

	echo "\033[32mDone!\033[0m\n";
