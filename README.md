# anytopics
Like AnyNews or LatestComments it shows latest topics or/and topic-comments on any page


-----------------------------------------------------------------------------------------
Code snippet 'Topics and Comments anywhere'
Licencsed under GNU, written by Chio Maisriml

What it does:
Like AnyNews or LatestComments it shows latest topics or/and topic-comments on any page
or from defined Pages/Sections only.


*****************************************************************************************
 SHORT INSTALLATION GUIDE:
*****************************************************************************************
 o download the zip file 
 o log into the backend and install the module as usual

*****************************************************************************************
 USING THE SNIPPET FUNCTION:
*****************************************************************************************
Once the module is installed, it can be invoked either from the index.php file of your template or from any code module section.

From template index.php:
<?php display_topics(); ?>

From a code module:
display_topics();


*****************************************************************************************
 ADDITIONAL PARAMETERS
*****************************************************************************************
For a more customised output, you can pass over serveral parameters to the function explained below.
Default values:
<?php display_topics($showwhat=3, $max_items = 6, $max_chars=900, $max_chars_per=120, $active_only=1, $readmore_text='More..', $latesttopicstitle='<h3>Latest Topics:</h3>', $latestcommentstitle='<h3>Latest Comments:</h3>', $section_id=0); ?>

  $showwhat=3     // 0:Topics only, 1: Comments only, 2: topic first, then comments, 3: (default): comments first, then topic. 
  by default (3): $max_chars is over all.
  
  $max_items=6   // how much topics or comments
  $max_chars=900 //the maximal number of chars
  NOTE: the snippet stops, when ONE of the values is reached.
  E.g. if $max_items = 5, but $max_chars=300 there might be displayed only 1 or 2 items.
  
  $max_chars_per=120 // the maximal number of chars per item
  $active_only=1 //  1: item is shown only when topic is active
  
  $readmore_text='More..'
  $latesttopicstitle='<h3>Latest Topics:</h3>'
  $latestcommentstitle='<h3>Latest Comments:</h3>'

	$section_id = 0		// section to show topics from (default:= 0 all sections, X:= section X, for multiple sections: array(2,4,5) )
	$sort_order = 0		// sort-order for output ( default:= 0  DESC, 1: ASC )

*****************************************************************************************
 TROUBLE SHOOTING
*****************************************************************************************
 - pass over either no argument, or all arguments in expected order
 - mask text with "your text " or 'your text '
 - remind the ; at the end of the code line

*****************************************************************************************
 STYLE THE OUTPUTS ACCORDING YOUR NEEDS
*****************************************************************************************
The output can be customized to your needs without touching the code itself, by the use of CSS definitions. 



