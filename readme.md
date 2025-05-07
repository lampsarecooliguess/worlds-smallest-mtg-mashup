# World's Smallest MTG Image Masher

I built my own copy of the World's Smallest Magic: The Gathering Commander deck. There's just one problem --  it sucks. This script was written to take a folder of MTG card images and mash them up into groups of 4. The output images can then be sent to a print on demand proxy website and cut out later for use. 

## How to use



### Dependencies

* PHP installed with GD extension (tested on PHP 8.3)

### Installing

* Copy the repo to a folder
* Put images in the input folder (jpg, png, and webp are supported)

### Executing program

* Once your input images are ready, open a terminal in the directory where this code lives
* Execute the command `php run.php` in your terminal
* The combined images will be stored in the output folder

## Help

Each printing service may have a slightly different output depending on their print bleed values and amounts. These variables are tweakable at the top of the `run.php` file.

[See this great writeup on reddit bu u/goatducknipples for more information](https://www.reddit.com/r/mpcproxies/comments/e9q1z7/complete_guide_to_image_sizing_for_mpc_or_other/
)

Good luck!
