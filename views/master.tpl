<!DOCTYPE html>
<html lang="en">
  <head>
    <title><tag:campname />: <tag:title /></title>

    <if:standalone>
		<link rel="icon" type="image/png" href="/resources/img/icon.png" />
		<link rel="stylesheet" type="text/css" href="/resources/css/bootstrap-refined.css" />
		<link rel="stylesheet" type="text/css" href="/resources/css/layout.css" />
		<link rel="stylesheet" type="text/css" href="/resources/css/winter.css" />
    <else:standalone>
		<link rel="icon" type="image/png" href="<tag:standalone-icon />" />
		<style type="text/css">
		<tag:standalone-style />
		</style>
    </if:standalone>

    <if:head>
		<tag:head />
	</if:head>
    
	<if:standalone>

		<script src='/external/jquery-1.7.js'></script>

		<if:js>
			<script src='/resources/js/<tag:js />'></script>
		</if:js>
		
    </if:standalone>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  </head>
  <body>
    <!-- Header -->
    <if:standalone>
    <div id="headerContainer">
		<div id="header">
			<a href='/'><img src="/resources/img/logo.png" class="logo" border="0" alt="Übertweak Logo" width="96px" height="96px" /></a>
			
			<div class="title">
			<span>
			<tag:campname /> -
			<if:shortTitle>
				<tag:shortTitle />
			<else:shortTitle>
				<tag:title />
			</if:shortTitle></span><br />
			<div class="version">Powered by
			<strong><tag:softwareFullName /></strong></a>
			</div>
			
			</div>
		</div>

    </div>
    <div id="menu">
        <ul id="nav">
			<li style='width: 10px;'>&nbsp;</li>
            <tag:menu />
			<if:loggedin>
				<li class="right"><a href='/logout'>Logout</a></li>
				<li class="right"><span class='text'>Current User: <tag:currentName /></li>
			</if:loggedin>
        </ul>
    </div>
    <br clear="both" />
    <!-- Content -->
    <tag:messages />

    <div id="content">
        <if:contenttitle>
          <h2><tag:contenttitle />:</h2>
        </if:contenttitle>
        <tag:content />
    </div>
    <else:standalone>    
    <div id="headerContainer">
    <div id="header" style='width: 100%;'>
		<img src="<tag:standalone-logo />" class="logo" border="0" alt="Übertweak Logo" />
		<div class="title">
		<tag:campname /> - <tag:title /><br />
		<div class="version">Powered by <strong><tag:softwareFullName /></strong></div></div>
	</div>
	</div>
	<!-- Content -->
	
	<div id="content">
                <if:contenttitle>
                  <h2><tag:contenttitle />:</h2>
                </if:contenttitle>
		<tag:content />
	</div>
    </if:standalone>

    <if:standalone><script src='/resources/js/ubersite.js'></script></if:standalone>
  </body>
</html>
