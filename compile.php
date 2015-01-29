<?php
        
/*
	Copyright Michael Demetriou 2015

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 3 as published by
    the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

// configuration
// Notice: you need to have inkscape installed

        $filename = 'fosdem presentation.svg';
        
        $problematicFonts = array();
        $problematicFonts[] = "Phosphate";
        $problematicFonts[] = "Avenir Next";
        $problematicFonts[] = "Comic Sans";
        
        $problematicElements = array();
        
//        ===============================================
        
        if (isset($argv[1]) && $argv[1] == "--help") {
            echo "Usage compile.php [language] [variant]";
            die();
        } 
        
        if (isset($argv[1]) && $argv[1] == "debug") $debug = true;
        
        header('Content-type: image/svg+xml');
        
        require_once './querypath-3.0.0/src/QueryPath.php';
        require_once './querypath-3.0.0/src/qp.php';
        
        $qp = qp($filename);
        
        $problematicElementsSelectStatements = " ";
        //select all elements with problematic fonts 
        foreach ($qp->find('text,flowRoot') as $text){
          foreach ($problematicFonts as $font){
            if (strstr(strtolower($text->css()), strtolower($font)) !== FALSE ){
              //we should also convert texts with a tspan with an offending code
              //pseudocode here:
              //if $text->is('tspan') $text->parents('text')->attr('id');
              $problematicElements[] = $text->attr('id');
              $problematicElementsSelectStatements .= " --select=".$text->attr('id');
            }              
          }
        }
        $problematicElementsSelectStatements .= " ";
		
        //if we are in the browser show the result
        if (isset($_GET['echo'])) $qp->writeXML();
        
      //  echo $problematicElementsSelectStatements,"\n"; die;
        
        //write result to file
        $newfile = "fosdem presentation compiled";
        $qp->writeXML($newfile.".svg");
        // die();
        //export to pdf
		//(we have to do once for a page -- good idea)
        //exec("inkscape --export-pdf=\"$newfile.pdf\" \"$newfile.svg\" ");
        
        if ($debug) die();
        
        //convert problematic fonts to paths before export
        exec("inkscape $problematicElementsSelectStatements --verb=ObjectToPath  --verb=FileSave --verb=FileClose \"$newfile.svg\"");
        