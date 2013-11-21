<?php
function searchForm(){
	return '
		<form method="get" action="index.php">
		<input type="hidden" name="page" value="search"/>
		<input type="text" name="search-term" />
		<input type="submit" value="search" />
	</form>';
}

function search( Database $db, Request $request){
	$out;
	$searchingFor = $request->get("search-term");
	if ($searchingFor){
		$out = showSearchResult( $db, $searchingFor);
	}
	return $out;
}

function showSearchResult (Database $db, $searchTerm){
	$result = getSearchResult( $db, $searchTerm);
	$ul = html("ul")->id("search-result");
	foreach ($result as $row){
		$id = $row->get("filename");
		$href ="index.php?page=videos&amp;video_filename=$id";
		$name = $row->get("filename");
		$a = html("a", $name)->attr("href", $href);
		$ul->append( html("li", $a) );
	}
	return $ul;
}

function getSearchResult( Database $db, $searchTerm){
	//always escape anything received from client to avoid injection
	$search = $db->escapeString( $searchTerm);
	$sql = "SELECT title, filename FROM video WHERE filename LIKE '%$search%'
			OR description LIKE '%$search%' ";
		return $db->getData ($sql);
}
