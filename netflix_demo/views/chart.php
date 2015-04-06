<?php

if (isset($_POST['terms']))
{
	include_once('../lib/Netflix.php');
	
	$netflix = new Netflix();
	
	// These credentials for a sample app I setup just for this purpose
	$netflix->set_key('q5z6p9pkm85jt8apushfczkp');
	$netflix->set_secret('WByWJN2prk');
	
	$num_movies = 10;
	$movie_titles = $movie_ratings = array();
	
	$netflix->search_by_title($_POST['terms']);
	
	for ($i = 0; $i < $num_movies; $i++)
	{
		if ( ! $netflix->get_movie_title(TRUE))
		{
			break;
		}
		
		$movie_titles[] = "'".preg_replace("/[^A-Za-z0-9\:\.+\,\s\s+]/", "", $netflix->get_movie_title(TRUE)) ."'";
		$movie_ratings[] = $netflix->get_movie_rating();
		
		$netflix->next_movie();
	}
	
	$chart_labels = implode('|', array_reverse($movie_titles, TRUE));
	$chart_data = implode(',', $movie_ratings);
}


if ( ! isset($chart_data) || ! $chart_data) : ?>

<div id="chart">
	No Data Found
</div>

<?php else: ?>

<div id="chart">
	<img src="https://chart.googleapis.com/chart?chs=520x320&cht=bhs&chd=t:<?php echo $chart_data; ?>&chxt=x,y&chxr=0,0,5,1&chxl=1:|<?php echo $chart_labels ?>&chds=0,5&chco=b9090b" />
</div>

<?php endif; ?>
