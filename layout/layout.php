
/**********/
/* GLOBAL */
/**********/

a {
	cursor: pointer;
}

body {
	font-family: "Gill Sans";
}

*.border {
	border: 1px solid silver;
}

#reminder, #reminder a {
	color: red;
	text-align: center;
	font-size: 36px;
}
/**********/
/* TOPNAV */
/**********/

ul#topnav {
	position: fixed;
	left: 0;
	top: 0;
	width: 100%;
	padding: 0;
	margin: 0;			
	height: 30px;
}

ul#topnav a {
	display: block;
	z-index:598;
	text-align: center;
	text-decoration: none;
			
	-webkit-transition: color .4s ease;
	-moz-transition: color .4s ease;
	-o-transition: color .4s ease;
	transition: color .4s ease;
}
		
ul#topnav a:hover {
	display: block;
	z-index:598;
	text-align: center;
}
		
ul#topnav li ul li a {
	text-align: center;
}
		
ul#topnav li {	
	padding: 0;			
	padding-top: 5px;
	float: left;
	list-style: none;		
	height: 30px;
	cursor: pointer;
	width: <?php if($_SESSION['permissions']['admin_view_requests'] ||
					     $_SESSION['permissions']['admin_edit_user'] ||
						 $_SESSION['permissions']['admin_set_new_user_pass'] ||
						 $_SESSION['permissions']['admin_manage_com'] ||
						 $_SESSION['permissions']['admin_manage_user'] ||
						 $_SESSION['permissions']['admin_manage_permissions'] ||
						 $_SESSION['permissions']['admin_manage_gallery'] ||
						 $_SESSION['permissions']['admin_manage_info'] ||
						 $_SESSION['permissions']['admin_manage_dates'] ||
						 $_SESSION['permissions']['admin_manage_char'] ||
						 $_SESSION['permissions']['admin_manage_cit'] ||
						 $_SESSION['permissions']['admin_manage_gossip'] ||
						 $_SESSION['permissions']['admin_manage_backup'] ||
						 $_SESSION['permissions']['admin_design_info'] ||
						 $_SESSION['permissions']['admin_backup_manual'] ||
						 $_SESSION['permissions']['admin_backup_restore'] ||
						 $_SESSION['permissions']['admin_hidemyass']) echo "16.666"; else echo "20"; ?>%;
}

ul#topnav li.transition {	

	cursor: pointer;
	
	-webkit-transition: background .4s ease;
	-moz-transition: background .4s ease;
	-o-transition: background .4s ease;
	transition: background .4s ease;
}
		
ul#topnav li ul {
	padding: 0;
	opacity: 0;
	position: relative;
	z-index:596;
	height:0px;
	margin-top: -20px;
}

ul#topnav li:hover>ul {
	padding-top: 8px;
	opacity: 1;
	margin-top: 0px;
}

ul#topnav li>ul li.transition {
	display: block;
	width: 100%;
	max-height: 0;
	overflow: hidden;
	
	-webkit-transition: max-height .8s ease;
	-moz-transition: max-height .8s ease;
	-o-transition: max-height .8s ease;
	transition: max-height .8s ease;
}
		
ul#topnav li>ul li a {
	opacity: 0;
	min-height:30px;
	
	-webkit-transition: opacity .2s ease;
	-moz-transition: opacity .2s ease;
	-o-transition: opacity .2s ease;
	transition: opacity .2s ease;
	
	-webkit-transition-delay: .1s;
	-moz-transition-delay: .1s;
	-o-transition-delay: .1s;
    transition-delay: .1s;
}

ul#topnav li:hover>ul li a {
	opacity: 1;
	display: block;
}
		
ul#topnav li:hover>ul li.transition {
	height: auto;
	max-height: 60px;			
	display:block;
	
	-webkit-transition: background .4s ease, max-height .8s ease;
	-moz-transition: background .4s ease, max-height .8s ease;
	-o-transition: background .4s ease, max-height .8s ease;
	transition: background .4s ease, max-height .8s ease;
}

ul#topnav li:hover>ul li a{
	-webkit-transition: color .4s ease 0s, opacity .2s ease .1s;
	-moz-transition: color .4s ease 0s, opacity .2s ease .1s;
	-o-transition: color .4s ease 0s, opacity .2s ease .1s;
	transition: color .4s ease 0s, opacity .2s ease .1s;
}
	
#searchbar input {
	width: 100%;
	float: none;
			
	-moz-border-radius: 4px;
	-webkit-border-radius: 4px;
	-o-border-radius: 4px;
	border-radius: 4px;
}

#searchbar {
	overflow: hidden;
}

#searchbar label {
	float: left;
	margin-left: 8px;
}
		
#searchbar span {
	display: block;
	overflow: hidden;
	padding: 0 5px
}	
		
#glass {
	float:right;
	width: 20px;
	height: 20px;
	margin-left: 5px;
	margin-right: 5px;
	background-image: url('<?php echo $rootfolder; ?>images/search.png');
	background-size: 18px 18px;
			
	-webkit-transition: background-color .2s ease;
	-moz-transition: background-color .2s ease;
	-o-transition: background-color .2s ease;
	transition: background-color .2s ease;
			
	-webkit-transition: border-color .2s ease;
	-moz-transition: border-color .2s ease;
	-o-transition: border-color .2s ease;
	transition: border-color .2s ease;
			
	-moz-border-radius: 4px;
	-webkit-border-radius: 4px;
	-o-border-radius: 4px;
	border-radius: 4px;
}
		
#glass:hover {
	background-color: white;
	border-color: white;
}

#searchcontainer {
	-moz-transition: none;
    -webkit-transition: none;
    -o-transition: color 0 ease-in;
    transition: none;
}
	
#logo {
	margin-left: 0px;
}
	

/***********/
/* CONTENT */
/***********/

div#content {
	margin-top: 36px;
	margin-left: 5px;
	margin-right: 5px;
}

h1 {
	text-align: center;
}

h2 {
	text-align: center;
}

table {
	table-layout:fixed;
	word-wrap:break-word;
}

div#input_container input {
	width: 100%;
	display: table-cell;
}

/* ------ BUTTONS ------ */
*.savebutton {
	width: 90px;
}

*.deletebutton {
	width: 80px;
}

*.restorebutton {
	width: 140px;
}

*.addbutton {
	width: 100px;
}

*.requestbutton {
	width: 120px;
}

*.editbutton {
	width: 95px;
}

*.homebutton {
	width: 20px;
	height: 20px;
}

*.sendbutton {
	width: 75px;
}

/* ---- END BUTTONS ---- */

/* ------ TABLESTUFF ------- */
*.br
{
	border-right: 1px solid silver;
	border-bottom: 1px solid silver;
}

*.r
{
	border-right: 1px solid silver;
}

*.b
{
	border-bottom: 1px solid silver;
}
/* ---- END TABLESTUFF ----- */

/**********/
/* FOOTER */
/**********/

div#footer {
	position: fixed;
	bottom: 0px;
	width: 100%;
	left: 0px;
	height: 30px;
}

*.buttonlink {	
	padding: 2px;
	cursor: pointer;

	-webkit-transition: background .4s ease;
	-moz-transition: background .4s ease;
	-o-transition: background .4s ease;
	transition: background .4s ease;
}

*.buttonlink img {	
	vertical-align: middle;
	margin-left: 3px;
	width: 20px;
	height: 20px;
}

*.buttonlink a,span {
	text-decoration: none;
	
	-webkit-transition: color .4s ease;
	-moz-transition: color .4s ease;
	-o-transition: color .4s ease;
	transition: color .4s ease;
}

div#keks, div#settings, div#logout, div#snake {
	margin-top: 4px;
}

div#keks {
	margin-left: 5px;
	float: left;	
	padding: 2px;
}

div#settings , div#logout, div#snake {
	float: right;
	margin-right: 5px;
}

div#footer img {
	vertical-align: middle;
	margin-left: 3px;
}
