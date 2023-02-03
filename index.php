<?php
//----------------------------------------
//          versionning
// CorrelImages :   1.4 : 2023/02/03
//                        rajout du nombre de tests sauvegardé dans localStorage (après bouton effacer le localStorage)
// CorrelImages :   1.3 : 2023/02/02
//                        lègère modification pour prise en compte (couleurs) du cas ou Nombres de Matches insuffisant
// CorrelImages :   1.2 : 2023/01/31
// CorrelImages :   1.0 : 2023/01/29
// CorrelImages :   0.1 : 2023/01/27
//   fichier PHP type pour une application
//----------------------------------------  


ini_set("default_charset", 'utf-8');
ini_set("display_errors", 1);
ini_set("error_reporting", E_ALL | E_STRICT);
date_default_timezone_set('Europe/Paris');
ini_set("always_populate_raw_post_data", "-1");  
set_time_limit(0);
session_start();   

/*    fonction pour debugging             */
function file_ecrit($filename,$data){     // pour gestion des erreurs côté serveur
    if($fp = fopen($filename,'a')){       // mode ajout !!
        $ok = fwrite($fp,$data);
        fclose($fp);
        return $ok;
    }
    else return false;
}      

/*     Le compteur                        */
  $db = new PDO('sqlite:compteur.sqlite');
  $pdo_result=$db->query("SELECT nb FROM compte WHERE seul=1");
  if (!$pdo_result){
      $sqlreq = 'CREATE TABLE "compte" ( "nb"  INTEGER DEFAULT (0), "seul" INTEGER DEFAULT (1)) WITHOUT ROWID';   
      $db->exec($sqlreq); 
  } else {
      $pdo_result->setFetchMode(PDO::FETCH_BOTH);
      $lastnb=$pdo_result->fetchColumn();  
      $pdo_result->closeCursor();  
      $lastnb=intval($lastnb);
  } 

  /* gestion des cookies, et donc du compteur ! */
  if(!@$_COOKIE["correlimages"]) {                                           //absence de cookie
     setcookie("correlimages","nimp",time()+(60*60*24*90),"/correlimages/");
     $cook = "nimp";                                                     // cookie par défaut
     // Incrémente le compteur
     $lastnb++;              
     //file_ecrit('compte.txt','**** (+1='.$lastnb.")"."\n");
     //Met à jour le compteur
     $pdo_result=$db->exec("UPDATE compte set nb=".$lastnb." WHERE seul=1"); 
     //file_ecrit('compte.txt','**** yes! '.$lastnb." le ".date("d/m/y-H:i:s")."\n");
  } else {                                                               // sinon, on le récupère !
     //file_ecrit('compte.txt','**** ne compte pas ! '.$lastnb." le ".date("d/m/y-H:i:s")."\n"); 
     $cook=$_COOKIE['correlimages'];
  }   
  $db = Null; 

/*  variable pour affichage titre / version / nbre de vue   */
  $Versiondu = 'V 1.4 du 02/02/2023';

?>
    <!DOCTYPE html>
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
        <meta http-equiv="cache-control" content="no-cache, must-revalidate"/>
        <meta http-equiv="Pragma" content="no-cache"/>
        <meta http-equiv="Expires" content="0"/>
        <meta name="DC.Language" content="fr"/>
        <meta name="description" content="indice coefficient de corrélation entre deux images"/>
        <meta name="author" content="ArouG" />
        <meta name="keywords" content="corrélation images tailles différentes"/>
        <meta name="date" content="2023/01/27"/>
        <meta name="robots" content="nofollow"/>
        <title>Correlimages</title>
        <style>
        html {
            font: 1.1em sans-serif;
        }
        
        body {
            display: block;
            background-color: black;
            margin: 8px;
        }
        
        .top-box {
            width: 1400px;
            height: 18px;
            margin-bottom: -18px;
            position: relative;
        }
        
        #bidon {
            width: 1400px;
            height: 20px;
            background-color: #000000;
            float: left;
        }
        
        #entete {
            width: 1400px;
            margin-top: 0;
            margin-bottom: 0;
            margin-left: 0;
            margin-right: 0;
            line-height: 5px;
            background-color: #600c0c;
            padding-top: 0;
            z-index: 50;
        }
        
        #main, #help, #NameRes {
            min-height: 500px;
        }

        #appelHelp {
            position : absolute;
            background-color : #600c0c;
            right:20px;
            top:20px;
            color : white;
        }

        #nomResult {
            position : absolute;
            background-color : #600c0c;
            left:20px;
            top:20px;
            color : white;
        }

        #cornleft {
            float: left;
            width: 150px;
            height: 75px;
            position: relative;
            background-color: #600c0c;
        }
        
        #titre {
            float: left;
            width: 1100px;
            position: relative;
            margin-top: 0;
            height: 75px;
            background-color: #600c0c;
        }

        #titre p {
            color: #f0e39e;
            font-family: Georgia, "Bitstream Vera Serif", Norasi, serif;
            font-size: 0.8em;
            font-style: italic;
            line-height: 0.2em;
        }        
        
        #titre a {
            margin-top: 10px;
            color: white;
            font-family: Georgia, "Bitstream Vera Serif", Norasi, serif;
            font-style: italic;
            font-size: 1em;
            font-style: italic;
        }

        #titre h2 {
            color: #f0e39e;
            font-family: Georgia, "Bitstream Vera Serif", Norasi, serif;
            font-style: italic;
            font-size: 1.1em;
            font-style: italic;
            text-align: center;
        }
        
        #cornright {
            float: left;
            width: 150px;
            height: 75px;
            position: relative;
            background-color: #600c0c;
        }
        
        #menu {
            text-align: center;
            background-color: #FFDEAD;
            width: 1400px;
            margin: auto;
            padding: 0;
        }
        #visu{
            margin: auto;
        }
        #paramsTab, #outputsTab{
            padding-top: 20px;
            margin: auto;
            border-collapse: collapse;        
            border: 1px solid black;   
            text-align: center;
            font-size: 0.7rem;
        }
        td{
            border: 1px solid black;      
        }
        #TH1inp, #TH2inp, #FCNB1inp, #FCNB2inp, #OSS1inp, #OSS2inp, #RanTinp {
            max-width:20px;
        }
        #MatchV, #RanItinp, #MatVinp, #RatioSinp{
            max-width:30px;
        }
        #TmsQG, #TmsT{
            background-color: #00FFF0;    
        }
        .QCG {
            background-color: rgba(0, 220, 0, 1); 
            color: white;   
        }
        .bad{            /* pour QC et NbGM */
            background-color: rgba(255, 0, 0, 1); 
            color: white;              
        }
        .goodQC{
            background-color: rgba(0, 220, 0, 1);
            color: black;
        }
        .goodNbGM{
            background-color: #FFDEAD;
            color: black;
        }
        /*
        .infobulle{
            color:#C00;
            text-decoration:none;
            border-bottom: 1px dotted;
            font-weight: bold;
        } */
        #basdepage {
            margin: 0;
            padding: 0;
            font-size: 0.55em;
            background-color: #600c0c;
            width: 1400px;
            float: left;
        }        
        #gauche {
            text-align: left;
            float: left;
        }
        
        #droite {
            text-align: right;
            float: left;
        }
        
        #centrebas {
            float: left;
            width: 1224px;
            text-align: center;
            margin: auto;
            padding: 0;
            color: white;
            font-family: Georgia, "Bitstream Vera Serif", Norasi, serif;
            font-style: italic;
            font-size: 18px;
            font-style: italic;
        }
        
        .styledvp {
            border: 0;
            line-height: 1.5;
            padding: 0;
            font-size: 1rem;
            text-align: center;
            color: #fff;
            min-width:100px;
            text-shadow: 1px 1px 1px #000;
            border-radius: 10px;
            background-color: rgba(0, 180, 0, 1);
            background-image: linear-gradient(to top left,
                                              rgba(0, 0, 0, .2),
                                              rgba(0, 0, 0, .2) 30%,
                                              rgba(0, 0, 0, 0));
            box-shadow: inset 2px 2px 3px rgba(255, 255, 255, .6),
                        inset -2px -2px 3px rgba(0, 0, 0, .6);
        }

        .styledvp:hover {
            background-color: rgba(0, 235, 0, 1);
        }
        .styledvp:focus{
            outline: 0px;
        }

        .styleddr {
            border: 0;
            line-height: 1.5;
            padding: 0;
            font-size: 1rem;
            text-align: center;
            color: #fff;
            min-width:80px;
            text-shadow: 1px 1px 1px #000;
            border-radius: 10px;
            background-color: rgba(220, 0, 0, 1);
            background-image: linear-gradient(to top left,
                                              rgba(0, 0, 0, .2),
                                              rgba(0, 0, 0, .2) 30%,
                                              rgba(0, 0, 0, 0));
            box-shadow: inset 2px 2px 3px rgba(255, 255, 255, .6),
                        inset -2px -2px 3px rgba(0, 0, 0, .6);
        }

        .styleddr:hover {
            background-color: rgba(255, 0, 0, 1);
        }
        .styleddr:focus{
            outline: 0px;
        }

        #selectfics {
            text-align: center;
            margin-inline: auto;
            margin-top: 15px;
            margin-bottom: 15px;
        }

        </style>
        <!-- source JSFEAT : https://github.com/inspirit/jsfeat -->
        <!--script src="./js/jsfeat.js"></script-->
        <script src="./js/jsfeat-min.js"></script>
        <script src="./js/modernizr-custom.js"></script>
        <script>

        "use strict";

        // https://gist.github.com/juliocesar/926500#file-best-localstorage-polyfill-evar-js
        if (!Modernizr.localstorage) {
          window.localStorage = {
            _data       : {},
            setItem     : function(id, val) { return this._data[id] = String(val); },
            getItem     : function(id) { return this._data.hasOwnProperty(id) ? this._data[id] : undefined; },
            removeItem  : function(id) { return delete this._data[id]; },
            clear       : function() { return this._data = {}; }
          };
        }

        var img1, img2;
        var myCanv, myCont, canvW, canvH, canv1W, canv1H, canv2W, canv2H, canv1L, canv1T, canv2L, canv2T, canvWmax, myCt1, myCt2, myC1, myC2;
        var descr1, descr2, corners1, corners2, ssc_key_points1, ssc_key_points2, corners_selected1, corners_selected2, matches, good_matches;
        var Parinit = {};
        var Result = {};
        var DatDeb;

        function storageAvailable(type) {
            try {
                var storage = window[type],
                    x = '__storage_test__';
                storage.setItem(x, x);
                storage.removeItem(x);
                return true;
            }
            catch(e) {
                return e instanceof DOMException && (
                    // everything except Firefox
                    e.code === 22 ||
                    // Firefox
                    e.code === 1014 ||
                    // test name field too, because code might not be present
                    // everything except Firefox
                    e.name === 'QuotaExceededError' ||
                    // Firefox
                    e.name === 'NS_ERROR_DOM_QUOTA_REACHED') &&
                    // acknowledge QuotaExceededError only if there's something already stored
                    storage.length !== 0;
            }
        }

        // for debug only : need datasave.php 
        function DataToFile(filename, data) {
            var xhr = new XMLHttpRequest();
            var arr = new Uint8Array(data);
            xhr.open("post", "datasave.php", true); // Sync open file
            xhr.setRequestHeader("Content-Type", "application/octet-stream");
            xhr.setRequestHeader("X-FILE-NAME", filename); // file name
            xhr.setRequestHeader("X-FILE-SIZE", data.length); // file size  
            xhr.send(data); // Send            
        }

        async function downloadFile(data, name = 'file', type = 'text/plain') {
            // https://mindsers.blog/fr/telechargement-fichier-javascript/  
            const { createElement } = document
            const { URL: { createObjectURL, revokeObjectURL }, setTimeout } = window

            const blob = new Blob([data], { type })
            const url = createObjectURL(blob)

            const anchor = document.createElement('a')
            anchor.setAttribute('href', url)
            anchor.setAttribute('download', name)
            anchor.click()
          
            setTimeout(() => { revokeObjectURL(url) }, 100)
        }

        async function downFile(){
            var auj = new Date();

            var dd = auj.getDate();
            var mm = auj.getMonth() + 1;      
            var yyyy = auj.getFullYear();
            var hh = auj.getHours();
            var mn = auj.getMinutes();
            var ss = auj.getSeconds();
            if (dd < 10) {
                dd = '0' + dd;
            }
            if (mm < 10) {
                mm = '0' + mm;
            }     
            if (hh < 10){
                hh = '0' + hh;
            }  
            if (mn < 10){
                mn = '0' + mn;
            }     
            if (ss < 10){
                ss = '0' + ss;
            }
            var name = 'CorrelImagesTest_'+yyyy+'_'+mm+'_'+dd+'_'+hh+'_'+mn+'_'+ss+'.csv';
            var ligne = localStorage.getItem('Correltest');
            await downloadFile(ligne, name);
        }

        var previewPicture = function(e) {
            if(e.files) {
                var pict = e.files[0];
                if((pict.type == 'image/jpeg') || (pict.type == 'image/gif') || (pict.type == 'image/png')) {
                    var pictId = e.id;
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        if(pictId == 'inp1') {
                            document.getElementById("img1").src = e.target.result;
                        } else document.getElementById("img2").src = e.target.result;
                    }
                    reader.readAsDataURL(pict);
                } else alert('It is not an image file !');
            }
        }

        function help() {
            if(document.querySelector("#HelpButt").firstChild.textContent == 'Help') {
                document.querySelector("#HelpButt").firstChild.textContent = "Back";
                document.querySelector("#main").style.display = 'none';
                document.querySelector("#NameResButt").style.display = 'none';
                document.querySelector("#help").style.display = 'block';
            } else {
                document.querySelector("#HelpButt").firstChild.textContent = "Help";
                document.querySelector("#help").style.display = 'none';
                document.querySelector("#main").style.display = 'block';
                document.querySelector("#NameResButt").style.display = 'block';
            }
        }

        function NameRes() {
            if(document.querySelector("#NameResButt").firstChild.textContent == 'Sauvegarde') {
                document.querySelector("#NameResButt").firstChild.textContent = "Back";
                document.querySelector("#main").style.display = 'none';
                document.querySelector("#HelpButt").style.display = 'none';
                document.querySelector("#NameRes").style.display = 'block';
            } else {
                document.querySelector("#NameResButt").firstChild.textContent = "Sauvegarde";
                document.querySelector("#NameRes").style.display = 'none';
                document.querySelector("#main").style.display = 'block';
                document.querySelector("#HelpButt").style.display = 'block';
            }
        }
        // non zero bits count
        function popcnt32(n) {
            n -= ((n >> 1) & 0x55555555);
            n = (n & 0x33333333) + ((n >> 2) & 0x33333333);
            return(((n + (n >> 4)) & 0xF0F0F0F) * 0x1010101) >> 24;
        }

        // adapté from https://gist.github.com/nandor/7e74368a449924483173
        function my_ransac(matches, thres, iter) {
            var ret = {};
            var bd = 456555;
            //var good_matches=[]; 
            var count = 0;
            var best = {};
            var bestCnt = 0;
            // hypothèse : x2 = x0 + ax * x1; y2 = y0 + ay * y1  avec, normalement : x0 et y0 petits et ax=ay pas loin de 1 !
            //            ax0*x0 + ax1*x1 + cx = 0 et ay0*y0+ay1*y1+cy = 0 
            // 4 paramètres à déterminer  2 points sont nécessaires
            var ax0, ax1, cx, ay0, ay1, cy;
            for(var it = 0; it < iter; it += 1) {
                var i0 = ~~(Math.random() * (matches.length - 1)),
                    i1;
                do {
                    i1 = ~~(Math.random() * (matches.length - 1));
                } while (i1 == i0);
                var p0 = matches[i0],
                    p1 = matches[i1];
                // Calcule des 6 paramètres à partir des 2 points tirés au hasard
                var slope = (p1.x2 - p0.x2) / (p1.x1 - p0.x1);
                ax0 = -1.0;
                ax1 = 1.0 / slope;
                cx = p0.x1 - p0.x2 / slope;
                slope = (p1.y2 - p0.y2) / (p1.y1 - p0.y1);
                ay0 = -1.0;
                ay1 = 1.0 / slope;
                cy = p0.y1 - p0.y2 / slope;
                // Détermine le nombre de points correspondant au modèle basé sur les 2 points tirés au hasard
                count = 0;
                good_matches = [];
                for(var i = 0; i < matches.length; i += 1) {
                    var p = matches[i];
                    // valeurs théorique de x2, y2 soit donc x'2 et y'2
                    var xp2 = (p.x1 - cx) / ax1;
                    var yp2 = (p.y1 - cy) / ay1;
                    var dist = Math.sqrt((xp2 - p.x2) * (xp2 - p.x2) + (yp2 - p.y2) * (yp2 - p.y2));
                    if(dist < thres) {
                        if(dist < bd) {
                            bd = dist;
                        }
                        good_matches.push(matches[i]);
                        count += 1;
                    }
                }
                // Si on a trouvé plus de points, on sauvegarde les paramètres de notre nouveau modèle
                if(bestCnt < count) {
                    bestCnt = count;
                    best.ax0 = ax0;
                    best.ax1 = ax1;
                    best.cx = cx;
                    best.ay0 = ay0;
                    best.ay1 = ay1;
                    best.cy = cy;
                    best.cnt = count;
                    best.gm = good_matches;
                    best.dist = bd;
                }
            }
            Result.ax0 = best.ax0;
            Result.ax1 = best.ax1;
            Result.cx = best.cx;
            Result.ay0 = best.ay0;
            Result.ay1 = best.ay1;
            Result.cy = best.cy;
            Result.ransac_bestcount = best.cnt;
            Result.ransac_bestdist = best.dist;
            return best;
        }

        function match_pattern(descriptors1, descriptors2, typ, par, ssc1, ssc2, matches) {
            var match_threshold = 30;
            var match_frac = 0.8;
            if(typ == 't') {
                var match_threshold = parseInt(par);
            } else {
                var match_frac = parseFloat(par);
            }
            var q_cnt = descriptors1.rows;
            var query_du8 = descriptors1.data;
            var query_u32 = descriptors1.buffer.i32; // cast to integer buffer
            var qd_off = 0;
            var qidx = 0;
            var num_matches = 0;
            for(qidx = 0; qidx < q_cnt; ++qidx) {
                var best_dist = 256;
                var best_dist2 = 256;
                var best_idx = -1;
                var ld_cnt = descriptors2.rows;
                var ld_i32 = descriptors2.buffer.i32; // cast to integer buffer
                var ld_off = 0;
                for(var pidx = 0; pidx < ld_cnt; ++pidx) {
                    var curr_d = 0;
                    for(var k = 0; k < 8; ++k) {
                        curr_d += popcnt32(query_u32[qd_off + k] ^ ld_i32[ld_off + k]); // effectue un XOR (a XOR b = 1 <=> a != b)
                    }
                    if(curr_d < best_dist) {
                        best_dist2 = best_dist;
                        best_dist = curr_d;
                        best_idx = pidx;
                    } else if(curr_d < best_dist2) {
                        best_dist2 = curr_d;
                    }
                    ld_off += 8; // next descriptor
                }
                // filter if typ == 't'
                if(typ == 't') {
                    if(best_dist < match_threshold) {
                        matches.push({
                            'nm': num_matches,
                            'id1': qidx,
                            'id2': best_idx,
                            'dist': curr_d,
                            'x1': ssc1[qidx].x,
                            'y1': ssc1[qidx].y,
                            'x2': ssc2[best_idx].x,
                            'y2': ssc2[best_idx].y
                        });
                        num_matches++;
                    }
                // filter if typ == 'f'    
                } else {
                    if(best_dist < match_frac * best_dist2) {
                        matches.push({
                            'nm': num_matches,
                            'id1': qidx,
                            'id2': best_idx,
                            'dist': curr_d,
                            'x1': ssc1[qidx].x,
                            'y1': ssc1[qidx].y,
                            'x2': ssc2[best_idx].x,
                            'y2': ssc2[best_idx].y
                        });
                        num_matches++;
                    }
                }
                qd_off += 8; // next query descriptor
            }
            return num_matches;
        }

        //  adapté en javascript à partir du code Python ; https://github.com/BAILOOL/ANMS-Codes
        function ssc(keypoints, num_ret_points, tolerance, cols, rows) {
            var width, c, num_cell_cols, num_cell_rows, covered_vec, row, col, row_min, row_max, col_min, col_max, rtoc, ctoc;
            var exp1 = rows + cols + 2 * num_ret_points;
            var exp2 = (4 * cols + 4 * num_ret_points + 4 * rows * num_ret_points + rows * rows + cols * cols - 2 * rows * cols + 4 * rows * cols * num_ret_points);
            var exp3 = Math.sqrt(exp2);
            var exp4 = num_ret_points - 1;
            var sol1 = -Math.round((exp1 + exp3) / exp4);
            var sol2 = -1 * Math.round((exp1 - exp3) / exp4);
            var high = sol1;
            if(sol2 >= sol1) high = sol2;
            var low = Math.floor(Math.sqrt(keypoints.length) / num_ret_points);
            var prev_width = -1;
            var selected_keypoints = [];
            var result_list = [];
            var result = [];
            var complete = false;
            var k = num_ret_points
            var k_min = Math.round(k - (k * tolerance));
            var k_max = Math.round(k + (k * tolerance));
            while(!complete) {
                width = low + (high - low) / 2;
                if((width == prev_width) || (low > high)) {
                    result_list = result;
                    break;
                }
                c = width / 2;
                num_cell_cols = parseInt(Math.floor(cols / c));
                num_cell_rows = parseInt(Math.floor(rows / c));
                covered_vec = [];
                for(var ii = 0; ii < num_cell_cols; ii += 1) {
                    covered_vec[ii] = [];
                    for(var jj = 0; jj < num_cell_rows; jj += 1) {
                        covered_vec[ii][jj] = false;
                    }
                }
                result = [];
                for(var i = 1; i < keypoints.length; i += 1) {
                    row = parseInt(Math.floor(keypoints[i].y / c));
                    col = parseInt(Math.floor(keypoints[i].x / c));
                    //if ((covered_vec[row] !== undefined) && (covered_vec[row][col] !== undefined) && (!covered_vec[row][col])) {
                    if ((covered_vec[row] !== undefined)  && (!covered_vec[row][col])) {
                    //if (!covered_vec[row][col]) {
                        result.push(i);
                        row_min = parseInt(row - Math.floor(width / c));
                        row_min = Math.max(row_min, 0);
                        row_max = parseInt(row + Math.floor(width / c));
                        row_max = Math.min(row_max, num_cell_rows);
                        col_min = parseInt(col - Math.floor(width / c));
                        col_min = Math.max(col_min, 0);
                        col_max = parseInt(col + Math.floor(width / c));
                        col_max = Math.min(col_max, num_cell_cols);
                        for(rtoc = row_min; rtoc < row_max + 1; rtoc += 1) {
                            for(ctoc = col_min; ctoc < col_max + 1; ctoc += 1) {
                                //if ((covered_vec[rtoc] !== undefined) && (covered_vec[rtoc][ctoc] !== undefined) && (!covered_vec[rtoc][ctoc])) covered_vec[rtoc][ctoc] = true;
                                if ((covered_vec[rtoc] !== undefined) && (!covered_vec[rtoc][ctoc])) covered_vec[rtoc][ctoc] = true;
                                //if (!covered_vec[rtoc][ctoc]) covered_vec[rtoc][ctoc] = true;
                            }
                        }
                    }
                }
                if((k_min <= result.length) && (result.length <= k_max)) {
                    result_list = result;
                    complete = true;
                } else if(result.length < k_min) {
                    high = width - 1;
                } else {
                    low = width + 1;
                }
                prev_width = width;
            }
            for(i = 0; i < result_list.length; i += 1) selected_keypoints.push(keypoints[result_list[i]]);
            return selected_keypoints;
        }

        function render() {
            var x, y, x1, y1, x2, y2;
            // image 1 : en vert, tous les points obtenus par fast_corners ... seront repeints en bleus
            myCont.strokeStyle = "rgb(0,255,0)";
            for(var i = 0; i < corners_selected1.length; i++) {
                x = Math.ceil(corners_selected1[i].x * canv1W / img1.width) + canv1L;
                y = Math.ceil(corners_selected1[i].y * canv1H / img1.height) + canv1T;
                myCont.beginPath();
                myCont.moveTo(x, y - 1);
                myCont.lineTo(x, y + 1);
                myCont.moveTo(x - 1, y);
                myCont.lineTo(x + 1, y);
                myCont.lineWidth = 1;
                myCont.stroke();
            }
            // image 1 : ... seront repeints en violet ceux sélectionnés par la méthode SSC
            myCont.strokeStyle = "rgb(255,0,255)";
            for(var i = 0; i < ssc_key_points1.length; i++) {
                x = Math.ceil(ssc_key_points1[i].x * canv1W / img1.width) + canv1L;
                y = Math.ceil(ssc_key_points1[i].y * canv1H / img1.height) + canv1T;
                myCont.beginPath();
                myCont.moveTo(x, y);
                myCont.lineTo(x - 1, y - 1);
                myCont.lineTo(x + 1, y - 1);
                myCont.lineTo(x + 1, y + 1);
                myCont.lineTo(x - 1, y + 1);
                myCont.lineTo(x - 1, y - 1);
                myCont.lineWidth = 1;
                myCont.stroke();
            }
            // image 2 : en vert, tous les points obtenus par fast_corners ... seront repeints en bleus
            myCont.strokeStyle = "rgb(0,255,0)";
            for(var i = 0; i < corners_selected2.length; i++) {
                x = Math.ceil(corners_selected2[i].x * canv2W / img2.width) + canv2L;
                y = Math.ceil(corners_selected2[i].y * canv2H / img2.height) + canv2T;
                myCont.beginPath();
                myCont.moveTo(x, y - 1);
                myCont.lineTo(x, y + 1);
                myCont.moveTo(x - 1, y);
                myCont.lineTo(x + 1, y);
                myCont.lineWidth = 1;
                myCont.stroke();
            }
            // image 2 : ... seront repeints en violet ceux sélectionnés par la méthode SSC
            myCont.strokeStyle = "rgb(255,0,255)";
            for(var i = 0; i < ssc_key_points2.length; i++) {
                x = Math.ceil(ssc_key_points2[i].x * canv2W / img2.width) + canv2L;
                y = Math.ceil(ssc_key_points2[i].y * canv2H / img2.height) + canv2T;
                myCont.beginPath();
                myCont.moveTo(x, y);
                myCont.lineTo(x - 1, y - 1);
                myCont.lineTo(x + 1, y - 1);
                myCont.lineTo(x + 1, y + 1);
                myCont.lineTo(x - 1, y + 1);
                myCont.lineTo(x - 1, y - 1);
                myCont.lineWidth = 1;
                myCont.stroke();
            }
            // visualisation de la pseudo homothétie : relie en bleu ciel les bons points récupérés par RANSAC
            myCont.strokeStyle = "rgb(128,255,255)";
            for(var i = 0; i < good_matches.gm.length; i++) {
                x1 = Math.ceil(good_matches.gm[i].x1 * canv1W / img1.width) + canv1L;
                y1 = Math.ceil(good_matches.gm[i].y1 * canv1H / img1.height) + canv1T;
                x2 = Math.ceil(good_matches.gm[i].x2 * canv2W / img2.width) + canv2L;
                y2 = Math.ceil(good_matches.gm[i].y2 * canv2H / img2.height) + canv2T;
                myCont.beginPath();
                myCont.moveTo(x1, y1);
                myCont.lineTo(x2, y2);
                myCont.lineWidth = 1;
                myCont.stroke();
            }
        }

        function renderRect(RO,RD){
            myCont.strokeStyle = "rgb(255,255,0)";  
            var x0,y0,x2,y2;
            x0 = Math.ceil(RO.xO * canv1W / img1.width) + canv1L; 
            y0 = Math.ceil(RO.yO * canv1H / img1.height) + canv1T;
            x2 = Math.ceil(RO.xD * canv1W / img1.width) + canv1L; 
            y2 = Math.ceil(RO.yD * canv1H / img1.height) + canv1T;
            myCont.beginPath();  
            myCont.moveTo(x0, y0);
            myCont.lineTo(x2, y0);
            myCont.lineTo(x2, y2);
            myCont.lineTo(x0, y2);
            myCont.lineTo(x0, y0);
            myCont.lineWidth = 1;
            myCont.stroke();
            x0 = Math.ceil(RD.xO * canv2W / img2.width) + canv2L; 
            y0 = Math.ceil(RD.yO * canv2H / img2.height) + canv2T;
            x2 = Math.ceil(RD.xD * canv2W / img2.width) + canv2L; 
            y2 = Math.ceil(RD.yD * canv2H / img2.height) + canv2T;
            myCont.beginPath();  
            myCont.moveTo(x0, y0);
            myCont.lineTo(x2, y0);
            myCont.lineTo(x2, y2);
            myCont.lineTo(x0, y2);
            myCont.lineTo(x0, y0);
            myCont.lineWidth = 1;
            myCont.stroke();
        }

        function initialise_parametres_initiaux() {
            Parinit.egalise_hist1 = 0;
            if(document.querySelector("#EH1").checked) {
                Parinit.egalise_hist1 = 1;
            }
            Parinit.egalise_hist2 = 0;
            if(document.querySelector("#EH2").checked) {
                Parinit.egalise_hist2 = 1;
            }
            Parinit.th1 = parseInt(document.querySelector("#TH1inp").value);
            Parinit.th2 = parseInt(document.querySelector("#TH2inp").value);
            Parinit.FastCNb1 = parseInt(document.querySelector("#FCNB1inp").value);
            Parinit.FastCNb2 = parseInt(document.querySelector("#FCNB2inp").value);
            Parinit.ObjSSC1 = parseInt(document.querySelector("#OSS1inp").value);
            Parinit.ObjSSC2 = parseInt(document.querySelector("#OSS2inp").value);
            Parinit.Matval = parseInt(document.querySelector("#MatchV").value);
            if(Parinit.Matval > 1) {
                Parinit.Mattyp = 't';
            } else {
                Parinit.Mattyp = 'f';
                Parinit.Matval = parseFloat(document.querySelector("#MatchV").value);
            }
            Parinit.RanT = parseInt(document.querySelector("#RanTinp").value);
            Parinit.RanIter = parseInt(document.querySelector("#RanItinp").value);
            Parinit.ratioS = parseFloat(document.querySelector("#RatioSinp").value);
            if (localStorage.length == 0){
                var ligne = 'nom_img1;largeur_img1;hauteur_img1;nom_img2;largeur_img2;hauteur_img2;P_EH1;P_EH2;Seuil1;Seuil2;FastCounterNb1;';
                ligne += 'FastCounterNb2;ObjSSC1;ObjSSC2;MatchType;MatchVal;RansacSeuil;RansacIter;RatioSurf;TmsG1;TmsG2;TmsEH1;TmsEH2;';
                ligne += 'TmsFC1;TmsFC2;TmsSSC1;TmsSSC2;TmsMatch;TmsGM;TmsQG;TmsTot;FCnb1;FCnb2;SSCNb1;SSCNb2;MatchNb;GMNb;CoefC;CoefG;';
                ligne += 'FxAx;FxCx;FyAy;FyCy;BoxOrigxO;BoxOrigyO;BoxOrigxD;BorOrigyD;BoxDestxO;BoxDestyO;BoxDestxD;BoxDestyD'+'\n';
                localStorage.setItem('Correltest', ligne);
            }
            document.querySelector("#nblig").textContent = localStorage.getItem("Correltest").split('\n').length-2;
            document.querySelector('#QC').className = 'QCG';
            document.querySelector('#QG').className = 'QCG';
            document.querySelector('#NbGM').className = 'goodNbGM';
        }

        function affiche_result() {
            document.querySelector("#Tms1G").textContent = parseInt(Result.Tms1G) + 'ms';
            document.querySelector("#Tms2G").textContent = parseInt(Result.Tms2G) + 'ms';
            document.querySelector("#Tms1Eg").textContent = parseInt(Result.Tms1Eg) + 'ms';
            document.querySelector("#Tms2Eg").textContent = parseInt(Result.Tms2Eg) + 'ms';
            document.querySelector("#TmsFC1").textContent = parseInt(Result.TmsFC1) + 'ms';
            document.querySelector("#TmsFC2").textContent = parseInt(Result.TmsFC2) + 'ms';
            document.querySelector("#FCDnb1").textContent = parseInt(Result.FCDnb1);
            document.querySelector("#FCDnb2").textContent = parseInt(Result.FCDnb2);
            document.querySelector("#TmsSSC1").textContent = parseInt(Result.TmsSSC1) + 'ms';
            document.querySelector("#TmsSSC2").textContent = parseInt(Result.TmsSSC2) + 'ms';
            document.querySelector("#NbSSC1").textContent = parseInt(Result.NbSSC1);
            document.querySelector("#NbSSC2").textContent = parseInt(Result.NbSSC2);
            document.querySelector("#TmsM").textContent = parseInt(Result.TmsM) + 'ms';
            document.querySelector("#NbMat").textContent = parseInt(Result.NbMat);
            document.querySelector("#TmsGM").textContent = parseInt(Result.TmsGM) + 'ms';
            document.querySelector("#NbGM").textContent = parseInt(Result.NbGM);
            Result.TmsT = Result.Tms1G + Result.Tms2G + Result.Tms1Eg + Result.Tms2Eg + Result.TmsFC1 + Result.TmsFC2 + Result.TmsSSC1 + Result.TmsSSC2 + Result.TmsM + Result.TmsGM + Result.TmsQG;
            document.querySelector("#TmsT").textContent = parseInt(Result.TmsT) + 'ms';
            document.querySelector("#QC").textContent = parseInt(Result.QC);
            document.querySelector("#Homax1").textContent = Math.ceil(parseFloat(Result.ax1) * 100) / 100;
            document.querySelector("#Homcx").textContent = parseInt(Result.cx);
            document.querySelector("#Homay1").textContent = Math.ceil(parseFloat(Result.ay1) * 100) / 100;
            document.querySelector("#Homcy").textContent = parseInt(Result.cy);
            document.querySelector("#RectOOut").textContent = 'Orig ('+img1.width+'*'+img1.height+')';
            document.querySelector("#RectDOut").textContent = 'Dest ('+img2.width+'*'+img2.height+')';
            document.querySelector("#OxO").textContent = Result.ROxO;
            document.querySelector("#OyO").textContent = Result.ROyO;
            document.querySelector("#OxD").textContent = Result.ROxD;
            document.querySelector("#OyD").textContent = Result.ROyD;
            document.querySelector("#DxO").textContent = Result.RDxO;
            document.querySelector("#DyO").textContent = Result.RDyO;
            document.querySelector("#DxD").textContent = Result.RDxD;
            document.querySelector("#DyD").textContent = Result.RDyD;
            document.querySelector("#TmsQG").textContent = parseInt(Result.TmsQG) + 'ms';
            document.querySelector("#QG").textContent = Math.ceil(parseFloat(Result.QG) * 100);
            if (Result.QG == 0){
                // pas suffisament de Good Matches !
                if (Result.QC == 0){
                    document.querySelector("#QC").className = "bad";
                    document.querySelector("#NbGM").className = "bad";
                } else {
                // rapport surfaces inférieur à 0.5    
                }
            }
            var ligne=localStorage.getItem('Correltest');
            ligne += document.querySelector("#inp1").files[0].name+';'+img1.width+';'+img1.height+';'+document.querySelector("#inp2").files[0].name+';'+img2.width+';'+img2.height+';'+Parinit.egalise_hist1+';'+Parinit.egalise_hist2+';'+Parinit.th1+';'+Parinit.th2+';'+Parinit.FastCNb1+';'+Parinit.FastCNb2+';'+Parinit.ObjSSC1+';'+Parinit.ObjSSC2+';'+Parinit.Mattyp+';'+Parinit.Matval.toString().replace(".",",")+';'+Parinit.RanT+';'+Parinit.RanIter+';'+Parinit.ratioS+';'+parseInt(Result.Tms1G)+';'+parseInt(Result.Tms2G)+';'+parseInt(Result.Tms1Eg)+';'+parseInt(Result.Tms2Eg)+';'+parseInt(Result.TmsFC1)+';'+parseInt(Result.TmsFC2)+';'+parseInt(Result.TmsSSC1)+';'+parseInt(Result.TmsSSC2)+';'+parseInt(Result.TmsM)+';'+parseInt(Result.TmsGM)+';'+parseInt(Result.TmsQG)+';'+parseInt(Result.TmsT)+';'+parseInt(Result.FCDnb1)+';'+parseInt(Result.FCDnb2)+';'+parseInt(Result.NbSSC1)+';'+parseInt(Result.NbSSC2)+';'+parseInt(Result.NbMat)+';'+parseInt(Result.NbGM)+';'+parseInt(Result.QC)+';'+Math.ceil(parseFloat(Result.QG) * 100)+';'+parseFloat(Result.ax1)+';'+parseFloat(Result.cx)+';'+parseFloat(Result.ay1)+';'+parseFloat(Result.cy)+';'+Result.ROxO+';'+Result.ROyO+';'+Result.ROxD+';'+Result.ROyD+';'+Result.RDxO+';'+Result.RDyO+';'+Result.RDxD+';'+Result.RDyD+'\n';
            localStorage.setItem('Correltest',ligne);
            document.querySelector("#nblig").textContent = localStorage.getItem("Correltest").split('\n').length-2;
        }

        async function save_result(){
            var auj = new Date();

            var dd = auj.getDate();
            var mm = auj.getMonth() + 1;      
            var yyyy = auj.getFullYear();
            var hh = auj.getHours();
            var mn = auj.getMinutes();
            var ss = auj.getSeconds();
            if (dd < 10) {
                dd = '0' + dd;
            }
            if (mm < 10) {
                mm = '0' + mm;
            }     
            if (hh < 10){
                hh = '0' + hh;
            }  
            if (mn < 10){
                mn = '0' + mn;
            }     
            if (ss < 10){
                ss = '0' + ss;
            }
            var name = 'CorrelImagesTest_'+yyyy+'_'+mm+'_'+dd+'_'+hh+'_'+mn+'_'+ss+'.csv';
            var ligne = localStorage.getItem('Correltest');
            await downloadFile(ligne, name);
        }

        function ClearStorage(){
            localStorage.removeItem('Correltest'); 
            var ligne = 'nom_img1;largeur_img1;hauteur_img1;nom_img2;largeur_img2;hauteur_img2;P_EH1;P_EH2;Seuil1;Seuil2;FastCounterNb1;';
            ligne += 'FastCounterNb2;ObjSSC1;ObjSSC2;MatchType;MatchVal;RansacSeuil;RansacIter;RatioSurf;TmsG1;TmsG2;TmsEH1;TmsEH2;';
            ligne += 'TmsFC1;TmsFC2;TmsSSC1;TmsSSC2;TmsMatch;TmsGM;TmsQG;TmsTot;FCnb1;FCnb2;SSCNb1;SSCNb2;MatchNb;GMNb;CoefC;CoefG;';
            ligne += 'FxAx;FxCx;FyAy;FyCy;BoxOrigxO;BoxOrigyO;BoxOrigxD;BorOrigyD;BoxDestxO;BoxDestyO;BoxDestxD;BoxDestyD'+'\n';
            localStorage.setItem('Correltest', ligne);   
            document.querySelector("#nblig").textContent = localStorage.getItem("Correltest").split('\n').length-2;
        }

        function init() {
            document.querySelector("#nblig").textContent = localStorage.getItem("Correltest").split('\n').length-2;
            document.querySelector("#f_ok").addEventListener('click', async function(e) {
                e.stopImmediatePropagation();
                document.querySelector("#f_ok").style.visibility = "hidden";
                img1 = document.getElementById("img1");
                img2 = document.getElementById("img2");
                let mainW = document.querySelector('#main').clientWidth;
                if((img1.src != '') && (img2.src != '')) {

                    // traitement principal - les canvas et leurs dimensions
                    myCanv = document.querySelector("#visu");
                    myCanv.width = mainW;
                    myCont = myCanv.getContext('2d');
                    canvWmax = Math.max(img1.width, img2.width);
                    if(canvWmax > mainW) {
                        canv1W = Math.ceil(mainW * img1.width / canvWmax);
                        canv2W = Math.ceil(mainW * img2.width / canvWmax);
                        canv1H = Math.ceil(img1.height * canv1W / img1.width);
                        canv2H = Math.ceil(img2.height * canv2W / img2.width);
                    } else {
                        canv1W = img1.width;
                        canv2W = img2.width;
                        canv1H = img1.height;
                        canv2H = img2.height;
                        myCanv.width = canvWmax;
                    }
                    canv1T = 0;
                    canv1L = Math.floor((Math.max(canv1W, canv2W) - canv1W) / 2);
                    canv2T = canv1H;
                    canv2L = Math.floor((Math.max(canv1W, canv2W) - canv2W) / 2);
                    myCanv.height = canv1H + canv2H;

                    myCont.drawImage(img1, 0, 0, img1.width, img1.height, canv1L, canv1T, canv1W, canv1H);
                    myCont.drawImage(img2, 0, 0, img2.width, img2.height, canv2L, canv2T, canv2W, canv2H);

                    let ok = initialise_parametres_initiaux();
                    myC1 = document.querySelector("#Cv1");
                    myC1.width = img1.width;
                    myC1.height = img1.height;
                    myCt1 = myC1.getContext('2d');
                    myCt1.drawImage(img1, 0, 0, img1.width, img1.height);
                    myC2 = document.querySelector("#Cv2");
                    myC2.width = img2.width;
                    myC2.height = img2.height;
                    myCt2 = myC2.getContext('2d');
                    myCt2.drawImage(img2, 0, 0, img2.width, img2.height);

                    // IMAGE 1
                    // 0 initialise img1_u8 et corners en fonction de la taille du canvas
                    var img1_u8 = new jsfeat.matrix_t(img1.width, img1.height, jsfeat.U8_t | jsfeat.C1_t);
                    corners1 = [];
                    var i = img1.width * img1.height;
                    while(--i >= 0) {
                        corners1[i] = new jsfeat.keypoint_t(0, 0, 0, 0);
                    }

                    // on effectue le grayscale : 0/ img1_u8 recevra l'image en N&B
                    var imageData1 = myCt1.getImageData(0, 0, img1.width, img1.height);
                    DatDeb = Date.now();
                    jsfeat.imgproc.grayscale(imageData1.data, img1.width, img1.height, img1_u8);
                    Result.Tms1G = Date.now() - DatDeb;

                    // éxécution de l'égalisation de l'histogramme si souhaité :
                    if(Parinit.egalise_hist1 == 1) {
                        DatDeb = Date.now();
                        jsfeat.imgproc.equalize_histogram(img1_u8, img1_u8);
                        Result.Tms1Eg = Date.now() - DatDeb;
                    } else Result.Tms1Eg = 0;

                    // initialisation du threshold pour fast_corners   ----- Param1
                    var threshold = Parinit.th1;
                    jsfeat.fast_corners.set_threshold(threshold);

                    // fast_corners avec objectif d'obtenir au moins Parinit.FastCNb1 points remarquables
                    DatDeb = Date.now();
                    var count1 = 0;
                    do {
                        count1 = jsfeat.fast_corners.detect(img1_u8, corners1, 5);
                        Result.ratio1 = ((100 * count1) / (img1.width * img1.height));
                        threshold = threshold / 2;
                        jsfeat.fast_corners.set_threshold(threshold);
                    } while ((count1 < Parinit.FastCNb1) && (threshold > 1)); // <----- éviter de boucler à l'infini !?
                    Result.TmsFC1 = Date.now() - DatDeb;
                    Result.FCDnb1 = count1;

                    // ssc pour obtention d'une distribution spatiale homogène des points clés ()
                    DatDeb = Date.now();
                    corners_selected1 = corners1.slice(0, count1);
                    corners_selected1.sort(function (a, b) { return a.score > b.score ? -1 : 1 });
                    // attention : ssc nécessite que les points à filtrer soient classés par ordre décroissant de force / score
                    ssc_key_points1 = ssc(corners_selected1, Parinit.ObjSSC1, 0.1, img1.width, img1.height); // Param3 objectif nb_points
                    Result.TmsSSC1 = Date.now() - DatDeb;
                    Result.NbSSC1 = ssc_key_points1.length;

                    // création des "descriptors" pour chacun des points retenus après SSC limités en nombre à 500 ! 
                    descr1 = new jsfeat.matrix_t(32, 500, jsfeat.U8_t | jsfeat.C1_t);
                    jsfeat.orb.describe(img1_u8, ssc_key_points1, ssc_key_points1.length, descr1);

                    // IMAGE 2
                    // 0 initialise img2_u8 et corners en fonction de la taille du canvas
                    var img2_u8 = new jsfeat.matrix_t(img2.width, img2.height, jsfeat.U8_t | jsfeat.C1_t);
                    corners2 = [];
                    var i = img2.width * img2.height;
                    while(--i >= 0) {
                        corners2[i] = new jsfeat.keypoint_t(0, 0, 0, 0);
                    }

                    // on effectue le grayscale : img2_u8 recevra l'image en N&B
                    var imageData2 = myCt2.getImageData(0, 0, img2.width, img2.height);
                    DatDeb = Date.now();
                    jsfeat.imgproc.grayscale(imageData2.data, img2.width, img2.height, img2_u8);
                    Result.Tms2G = Date.now() - DatDeb;

                    // éxécution de l'égalisation de l'histogramme si souhaité :
                    if(Parinit.egalise_hist2 == 1) {
                        DatDeb = Date.now();
                        jsfeat.imgproc.equalize_histogram(img2_u8, img2_u8);
                        Result.Tms2Eg = Date.now() - DatDeb;
                    } else Result.Tms2Eg = 0;

                    // initialisation du threshold pour fast_corners2
                    threshold = Parinit.th2;
                    jsfeat.fast_corners.set_threshold(threshold);

                    // fast_corners avec objectif d'obtenir au moins Parinit.FastCNb2 points remarquables
                    var count2 = 0;
                    DatDeb = Date.now();
                    do {
                        count2 = jsfeat.fast_corners.detect(img2_u8, corners2, 5);
                        Result.ratio2 = ((100 * count2) / (img2.width * img2.height));
                        threshold = threshold / 2;
                        jsfeat.fast_corners.set_threshold(threshold);
                    } while ((count2 < Parinit.FastCNb2) && (threshold > 1)); // ----------------------- Param2
                    Result.TmsFC2 = Date.now() - DatDeb;
                    Result.FCDnb2 = count2;

                    // ssc pour obtention d'une distribution spatiale homogène des points clés ()
                    DatDeb = Date.now();
                    corners_selected2 = corners2.slice(0, count2);
                    corners_selected2.sort(function (a, b) { return a.score > b.score ? -1 : 1 });
                    // attention : ssc nécessite que les points à filtrer soient classés par ordre décroissant de force / score
                    ssc_key_points2 = ssc(corners_selected2, 450, 0.1, img2.width, img2.height); // Param3 objectif nb_points
                    Result.TmsSSC2 = Date.now() - DatDeb;
                    Result.NbSSC2 = ssc_key_points2.length;

                    // création des "descriptors" pour chacun des points retenus après SSC limités en nombre à 500 ! 
                    descr2 = new jsfeat.matrix_t(32, 500, jsfeat.U8_t | jsfeat.C1_t);
                    jsfeat.orb.describe(img2_u8, ssc_key_points2, ssc_key_points2.length, descr2);

                    // On commence à rechercher les points ayant des descripteurs "assez proches"
                    var num_matches = 0;
                    good_matches = {};
                    matches = [];

                    /* -------------------------------------------------------------------------------------------------------- 
                     Recherche des descripteurs "proches" : nous avons le choix entre deux méthodes : 
                      a)soit à partir d'une distance / Threshold maxi (Parinit.Mattyp = 't', Parinit.Matval = valeur max)
                        Ex : num_matches = match_pattern(descr1, descr2, 't', 200 , ssc_key_points1, ssc_key_points2);
                      b)soit en diminuant de + en + cette distance max (Parinit.Mattyp = 'f', Parinit.Matval = coefficient < 1)
                        Ex : num_matches = match_pattern(descr1, descr2, 'f', 0.9 , ssc_key_points1, ssc_key_points2);
                    /* --------------------------------------------------------------------------------------------------------*/  
                    DatDeb = Date.now();
                    num_matches = match_pattern(descr1, descr2, Parinit.Mattyp, Parinit.Matval, ssc_key_points1, ssc_key_points2, matches);
                    Result.TmsM = Date.now() - DatDeb;
                    Result.NbMat = matches.length;
                                            /* -----------------sauvegarde du fichier pour debug------------- *//*
                                            var outmg='';
                                            for (var is=0; is<matches.length; is++){ outmg += 'x1,y1,x2,y2='+matches[is].x1+','+matches[is].y1+','+matches[is].x2+','+matches[is].y2+'\n'};
                                            await downloadFile(outmg, 'matches.txt'); outmg='';
                                            /* --------------------------------------------------------------- */

                    // Recherche d'une pseudo homothétie par l'algorithme de Ransac si nous avons au moins 10 bonnes correspondances
                    if(matches.length > 10) {
                        DatDeb = Date.now();
                        //  params5 et 6
                        good_matches = my_ransac(matches, Parinit.RanT, Parinit.RanIter); // threshold 20, 3000 itérations 
                        Result.TmsGM = Date.now() - DatDeb;
                        Result.NbGM = good_matches.gm.length;
                                            /* -----------------sauvegarde du fichier pour debug------------- *//*
                                            var outmg='';
                                            for (var is=0; is<good_matches.gm.length; is++){ outmg += 'x1,y1,x2,y2='+good_matches.gm[is].x1+','+good_matches.gm[is].y1+','+good_matches.gm[is].x2+','+good_matches.gm[is].y2+'\n'};
                                            await downloadFile(outmg, 'good_matches.txt'); outmg='';
                                            /* --------------------------------------------------------------- */
                        // résultat recherché <=> Coefficient de corrélation !
                        var QCorr = Math.ceil(100 * Result.NbGM / Result.NbMat);

                        // affichage visuel des points correspondants
                        render();

                        // calcul maintenant de la "distance en gris" entre les 2 images. Rappel -x1 + ax1*x2 +cx = 0 et -y1 + ay1*y2 + cy = 0
                        var xp0, yp0, xp2, yp2;
                        var rectO = {}; var rectD = {};
                        DatDeb = Date.now();
                        xp0 = fx1(0); yp0 = fy1(0); xp2 = fx1(img1.width); yp2 = fy1(img1.height); //Diagonale de haut à gauche vers bas à àroite
                        if ((xp0 < 0) && (yp0 < 0)){
                            rectO.xO = fx0(0); rectO.yO = fy0(0); rectD.xO = 0;      rectD.yO = 0;                            
                        } else if ((xp0 >= 0) && (yp0 >=0)){
                            rectO.xO = 0;      rectO.yO = 0;      rectD.xO = fx1(0); rectD.yO = fy1(0);
                        } else if ((xp0 >= 0) && (yp0  < 0)){ 
                            rectO.xO = 0;      rectO.yO = fy0(0); rectD.xO = fx1(0); rectD.yO = 0;
                        } else {    // xp0 < 0 et yp0 >= 0
                            rectO.xO = fx0(0); rectO.yO = 0;      rectD.xO = 0;      rectD.yO = fy1(0);
                        }

                        if ((xp2 <= img2.width-1) && (yp2 <= img2.height-1)){
                            rectO.xD= img1.width-1;      rectO.yD= img1.height-1;      rectD.xD= fx1(img1.width-1); rectD.yD= fy1(img1.height-1);
                        } else if ((xp2 <= img2.width-1) && (yp2 > img2.height-1)){
                            rectO.xD= img1.width-1;      rectO.yD= fy0(img2.height-1); rectD.xD= fx1(img1.width-1); rectD.yD= img2.height-1;
                        } else if ((xp2 > img2.width-1) && (yp2 <= img2.height-1)){ 
                            rectO.xD= fx0(img2.width-1); rectO.yD=  img1.height-1;     rectD.xD= img2.width-1;      rectD.yD= fy1(img1.height-1);
                        } else {   // xp2 > img2.width-1 et yp2 > img2.heidth-1
                            rectO.xD= fx0(img2.width-1); rectO.yD= fy0(img2.height-1); rectD.xD= img2.width-1;      rectD.yD= img2.height-1; 
                        }
                        Result.ROxO = rectO.xO; Result.ROyO = rectO.yO; Result.ROxD = rectO.xD; Result.ROyD = rectO.yD;
                        Result.RDxO = rectD.xO; Result.RDyO = rectD.yO; Result.RDxD = rectD.xD; Result.RDyD = rectD.yD;

                        // CONTINUE si et seulement si la surface de chaque rectangle est supérieure au ratio minimum d'image !
                        if (((Math.abs((Result.ROxD-Result.ROxO) * (Result.ROyD-Result.ROyO)) / (img1.width*img1.height)) >= Parinit.ratioS) && ((Math.abs((Result.RDxD-Result.RDxO) * (Result.RDyD-Result.RDyO)) / (img2.width*img2.height)) >= Parinit.ratioS)){ 
                            var x1, y1, x2, y2;
                            // Choissons le plus petit rectangle pour miniiser les calculs ... et donc le temps ! 
                            if (Math.abs(rectD.xD - rectD.xO) > Math.abs(rectO.xD - rectO.xO)){
                                var som = 0;
                                for (x1=rectO.xO; x1<rectO.xD; x1++){
                                    for (y1=rectO.yO; y1<rectO.yD; y1++){
                                        x2=fx1(x1); y2=fy1(y1);
                                        som += Math.abs((img1_u8.data[y1*img1.width + x1] - img2_u8.data[y2*img2.width + x2]));
                                    }
                                }
                                som = som / (Math.abs((rectO.xD - rectO.xO) * (rectO.yD - rectO.yO))* 255);
                            } else {
                                var x1, y1, x2, y2;
                                var som = 0;
                                for (x1=rectD.xO; x1<rectD.xD; x1++){
                                    for (y1=rectD.yO; y1<rectD.yD; y1++){
                                        x2=fx0(x1); y2=fy0(y1);
                                        som += Math.abs((img2_u8.data[y1*img2.width + x1] - img1_u8.data[y2*img1.width + x2]));
                                    }
                                }
                                som = som / (Math.abs((rectO.xD - rectO.xO) * (rectO.yD - rectO.yO))* 255);
                            }
                            Result.TmsQG = Date.now() - DatDeb;
                            Result.QG = 1-som;
                            Result.QC = QCorr;
                            renderRect(rectO, rectD);
                        } else {
                            Result.QC = QCorr;
                            Result.QG = 0;   
                            Result.TmsQG = Date.now() - DatDeb;                       
                        }
                    } else {
                        var QCorr = 0;
                        Result.NbGM = 0;
                        Result.ax0 = 0;
                        Result.ax1 = 0;
                        Result.cx = 0;
                        Result.ay0 = 0;
                        Result.ay1 = 0;
                        Result.cy = 0;
                        Result.ransac_bestcount = 0;
                        Result.ransac_bestdist = 'infinity';
                        Result.ROxO = '-'; Result.ROyO = '-'; Result.ROxD = '-'; Result.ROyD = '-';
                        Result.RDxO = '-'; Result.RDyO = '-'; Result.RDxD = '-'; Result.RDyD = '-';
                        Result.axex = '-'; Result.axey = '-'; 
                        Result.QG = 0;
                        Result.QC = 0;
                        Result.TmsQG = 0;
                    }

                    // affichages des résultats dans le tableau
                    affiche_result();
                } else alert('2 images doivent préalablement avoir été sélectionnées');
                document.querySelector("#f_ok").style.visibility = "visible";
            },true);
        }

        function fx0(x1){   // rappel : -x0 + Result.ax1.x1 + Result.cx = 0 
            var x0 = Math.ceil((Result.ax1 * x1) + Result.cx);
            return x0;
        }
        function fx1(x0){   // rappel : -x0 + Result.ax1.x1 + Result.cx = 0
            var x1 = Math.ceil((x0 -Result.cx)/Result.ax1);
            return x1;
        }
        function fy0(y1){   // rappel : -y0 + Result.ay1.y1 + Result.cy = 0 
            var y0 = Math.ceil((Result.ay1 * y1) + Result.cy);
            return y0;
        }
        function fy1(y0){   // rappel : -y0 + Result.ay1.y1 + Result.cy = 0 
            var y1 = Math.ceil((y0 -Result.cy)/Result.ay1);
            return y1;
        }
        </script>
    </head>

    <body onload="init();">
        <div id="menu">
            <div id="top_of_box" class="top-box"> </div>
            <div id="bidon"></div>
            <div id="entete"> <img id="cornleft" src="data:image/gif;base64,R0lGODlhlgBLAOfLABEMCw4PBBUNBAwPDCkJBTEJBBwQChkTDBUVDCATCCcRChgWBzAQBx8UFiIXEx4aBhgbDygZChkcGiMfDR8gGRspED4fEColEx0qCygmGy8lFCQoGy4oDigqDjkmETMoECkqKRgxGT0oDBsyEiMwEkwlDDEsGSYyDiM3EU4rEkktHCQ6GkEzEDk1GiI7JDQ3ITo2JDg2KkI1Fyo7I0ozFj04EUA0MDw4GB5AJTE8FjU4Mh5CGB5KGiZIHyBKIFI8JStIK0dCIV88GBxQJDNKIDdIKk1BM1FCH1hAIDtJIyNPMEdFLE1GGFdDGj1LGUFGQEVGNiZRLCZTIFpFLVBKTCZbIylcHT9VKSFdOFtOKW1LHi5cMGJNM1NTKlFVHjFeJklYIVNSQWVQIllSOl9RNVRSTFRUOVlVISxjKWBWFlFZMm9VGixrMTRqMDtoMjloO0lmMmxeK21dPmdhKnVeKDdyMDNzOHFjImZiTTtxOXthI0JwPGZoJGZlQ4RgJW5kRWFrPGlpYEV7QU15Pzx+Q3trTD9/O3hvOYRtOHdvT5JrLoluLXBxWUGDOJBuJYJzJYxyJnl1S59zLUeMS5Z5MJR6OJF9K5l7K4l/OY18U4B/Ypx9JY58YYSBXIt/XIaCVEuXUY+JMoSGgaiHKJ2JOKeHMKGKJoyMeoSXTZeRboyUc5iVTZeUZpqYWbSWNqmaQrCeOrCfMaKhSLucMqGigaSjcqaoZ7CnX8SqTKqrmaerp7KzgcmzQrO1lsC6WMLEpsrGfMvKjuHOMs7OocvNxNPUt+Tn4vDtsPn0rvLz0vf98v///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////yH+EUNyZWF0ZWQgd2l0aCBHSU1QACH5BAEKAP8ALAAAAACWAEsAAAj+AH2g4EGQ4A4eO3ysWLHDBJEeX4gQkfKlokUiX9QQGjSp4xslFzJkMJFBQYELJEiscAHkxQslW7DI3AJkxYyUM2Zs2aIEiJKUK0wwULDC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" alt="coingauche" />

            <div id="titre">
                <h2><a>CorrelImages by ArouG</a> - visiteurs : <a><?php echo $lastnb; ?></a> depuis le 27/01/2023</h2> 
                <p style="text-align:center;">--------------------------------</p>                                                           
                <p style="text-align:center;"><?php echo $Versiondu; ?> - Contact :  ArouG at turbosudoku dot fr</p>         <!-- params  -->
                <hr />
                <div id="appelHelp">                    
                    <button id='HelpButt' class="styledvp" onclick="help();">Help</button>
                </div>
                <div id="nomResult">                    
                    <button id='NameResButt' class="styledvp" onclick="NameRes();">Sauvegarde</button>
                </div>
            </div><!-- div titre -->       

            <img id="cornright" src="data:image/gif;base64,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" alt="coindroit" />
                <div><span class="bidon0">&nbsp;</span></div>
            </div> <!-- entete -->
            <div id="NameRes" style="display:none">
                <p>CorrelImages a aussi été conçu pour permettre la détermination, en fonction de la paire d'images présentées, des "meilleurs paramètres d'entrée possibles". Si le nombre de descripteurs est fixé à 500 (pour chaque image), il reste beaucoup d'autres paramètres d'entrée !</p>
                <p>Aussi m'était-il indispensable d'offrir la possibilité de stocker les différentes valeurs obtenues en fonction des paramètres</p>
                <p>J'ai choisi le format .csv car l'un des plus facile à utiliser et permettant un traitement postérieur avec des outils du type tableur : Excel de MicrosoftOffice , calcs d'OpenOffice mais toute base de données que vous utilisez ;-)</p>
                <p>Afin de permettre la conservation des données d'une séance à l'autre, j'utilise le 'localStorage'. Attention : cette technologie est propre à chaque navigateur ! Si vous débutez avec firefox, ne vous attendez pas à retrouver vos données sous Chrome ou Edge !</p>
                <p>La "récupération" de vos donnéees s'effectuera par téléchargement et il sera donc nécessaire de récupérer vos données &lt;=> le fichier résultant sous le répertoire dédié (C:\downloads ou C:\Téléchargements pour les utilisateurs de Windows)</p>
                <p> Le fichier téléchargé se nommera CorrelImagesTest_date_et_heure.csv. Deux commandes sont à votre disposition :</p>
                <br>
                <p> <button id="DownButt" class="styleddr" type="button" onClick="save_result();">Télécharger le fichier</button> et, de temps en temps, <button id="ClearButt" class="styleddr" type="button" onClick="ClearStorage();">Vider vos données du localStorage</button>&nbsp;&nbsp;&nbsp;Nombre de tests enregistrés en cours :&nbsp;<span id="nblig">&nbsp;</span></p>

            </div>
            <div id="help" style="display:none">
                <h1>Objectif</h1>
                <p>CorrelImages a pour objectif de mettre au point un nombre, compris entre 0 et 100, permettant d'indiquer le degré de corrélation entre 2 images de dimensions éventuellement différentes, en recherchant une relation telle que, pour tout point P1(x1,y1) (du moins un très grand nombre) de la première image, on puisse associer un point P2(x2,y2) de la seconde tels que :</p>
                <p> a.x1 + b.x2 + c = 0 ET  d.y1 + e.y2 + f = 0</p>
                <P> autrement dit que les deux images soient plus ou moins homothétiques.</p>
                <p>CorrelImages utilise / met en oeuvre plusieurs techniques liées au traitement d'images. Il utilise principalement la librairie <a href="https://github.com/inspirit/jsfeat" target="_blank">JSFeat.js</a> (passage couleur en b&B, égalisation d'histogramme, détection rapides des points remarquables d'une image, descripteurs ORB et match_pattern) mais aussi des techniques non incluses dans cette librairie : diminution des points remarquables par suppression des non maximaux en vue d'obtenir une distribution "homogène" et l'algorithme de RANSAC pour ce qui concerne la recherche de la "pseudo homothétie"</p>
                <br>
                <h1>Politique de cookies :</h1>
                <p>Correlmages n'utilise qu'un seul cookie 'forcé' (nom = CorrelImages, valeur = nimp) permettant le comptage des visites sur le site</p>
            </div>
            <div id="main">
                <div id="paramsDiv">
                    <table id="paramsTab"><tbody>
                        <tr><td id="parentree" colspan="12">paramètres d'entrée</td>
                        </tr><tr>
                            <td>=1</td><td>=2</td><td>Th1</td><td>Th2</td><td>FCNb1</td><td>FCNb2</td><td>OSC1</td><td>OSC2</td><td>MatchP</td><td>RanT</td><td>RanIter</td><td>RatioS</td>
                        </tr><tr>
                            <td id="egalise_hist1"><input id="EH1" title="égalisation de l'histogramme 1 ?" type="checkbox" checked></td><td id="egalise_hist2"><input id="EH2" title="égalisation de l'histogramme 2 ?" type="checkbox" checked></td><td id="th1"><input title="valeur du seuil1" type="text" id="TH1inp" minlength="1" maxlength="3" value="30"></td><td id="th2"><input title="valeur du seuil2" type="text" id="TH2inp" minlength="1" maxlength="3" value="30"></td> <td id="FastCNb1"><input type="text" id="FCNB1inp" title="nombre de points minimum de fast.counter1" minlength="1" maxlength="3" value="600"></td><td id="FastCNb2"><input type="text" id="FCNB2inp" title="nombre de points minimum de fast.counter2" minlength="1" maxlength="3" value="600"></td><td id="ObjSSC1"><input title="évaluation (+/-10%) du nombre de descripteurs résultant (<450)" type="text" id="OSS1inp" minlength="1" maxlength="3" value="450"></td><td id="ObjSSC2"><input title="évaluation (+/-10%) du nombre de descripteurs résultant (<450)" type="text" id="OSS2inp" minlength="1" maxlength="3" value="450"></td><td id="MatchP"><input title="méthode de criblage pour les descripteurs correspondants" type="text" id="MatchV" minlength="1" maxlength="4" value="200"></td><td id="RanT"><input title="seuil pour recherche des 'bonnes ressemblances'" type="text" id="RanTinp" minlength="1" maxlength="3" value="25"></td><td id="RanIter"><input title="nombre d'itérations à effectuer pour obtention des 'bonnes ressemblances'" type="text" id="RanItinp" minlength="1" maxlength="4" value="2500"></td><td id="RatioS"><input title="ratio minimal de surface pour accepter correspondance" type="text" id="RatioSinp" minlength="1" maxlength="4" value="0.50"></td>                      
                        </tr>   
                    </tbody></table>
                    <table id="outputsTab"><tbody>
                        <tr><td id="timesOut" colspan="12">temps (ms)</td><td id="QuantitesOut" colspan="6">nombre de points</td><td id="CoeffOut" colspan="2">Coeffs</td><td id="TransfOut" colspan="4">Trans</td><td id="RectOOut" colspan="4">Orig</td><td id="RectDOut" colspan="4">Dest</td>
                        </tr><tr>
                            <td>T1G</td><td>T1Eg</td><td>T2G</td><td>T2Eg</td><td>TFC1</td><td>TFC2</td><td>TSSC1</td><td>TSSC2</td><td>TmsM</td><td>TmsGM</td><td>TmsQG</td><td>TmsT</td><td>Fnb1</td><td>Fnb2</td><td>NbC1</td><td>NbC2</td><td>NbMat</td><td>NbGM</td><td>QC</td><td>QG</td><td>ax1</td><td>cx</td><td>ay1</td><td>cy</td><td>O.x0</td><td>O.y0</td><td>O.xD</td><td>O.yD</td><td>D.x0</td><td>D.y0</td><td>D.xD</td><td>D.yD</td>
                        </tr><tr>
                            <td id="Tms1G">0</td><td id="Tms1Eg">0</td><td id="Tms2G">0</td><td id="Tms2Eg">0</td><td id="TmsFC1">0</td><td id="TmsFC2">0</td><td id="TmsSSC1">0</td><td id="TmsSSC2">0</td><td id="TmsM">0</td><td id="TmsGM">0</td><td id="TmsQG">0</td><td id="TmsT">0</td><td id="FCDnb1">0</td><td id="FCDnb2">0</td><td id="NbSSC1">0</td><td id="NbSSC2">0</td><td id="NbMat">0</td><td id="NbGM">0</td><td id="QC">0</td><td id="QG">0</td><td id="Homax1">0</td><td id="Homcx">0</td><td id="Homay1">0</td><td id="Homcy">0</td><td id="OxO">0</td><td id="OyO">0</td><td id="OxD">0</td><td id="OyD">0</td><td id="DxO">0</td><td id="DyO">0</td><td id="DxD">0</td><td id="DyD">0</td>                                                   
                        </tr>   
                    </tbody></table>
                </div>
                <div id="ficselect">
                    <table id="selectfics">
                    <tr>   
                        <td><input id="inp1" type="file"  onchange="previewPicture(this)" accept=".jpg, .png, .gif"></td>
                        <td><button id="f_ok" class="styledvp">ok</button></td>
                        <td><input id="inp2" type="file"   onchange="previewPicture(this)" accept=".jpg, .png, .gif"></td>
                    </tr></table>
                </div>
                <div>
                    <canvas id="visu"></canvas>
                </div>                        
            </div>
            <div id="basdepage">
                <hr />
                <div id="gauche">
                    <a href="https://validator.w3.org/check?uri=https://aroug.eu/correlimages/index.php"> <img src="https://www.w3.org/Icons/valid-xhtml10" alt="Valid XHTML 1.0 Transitional" height="31" width="88" /> </a>
                </div>
                <div id="centrebas">Document soumis à licence <a href="https://creativecommons.org/licenses/by/2.0/fr/">Creative Commons "by"</a></div>
                <div id="droite">
                    <a href="https://jigsaw.w3.org/css-validator/validator?uri=https://aroug.eu/correlimages/index.php"> <img style="border:0;width:88px;height:31px" src="https://jigsaw.w3.org/css-validator/images/vcss" alt="CSS Valide !" /> </a>
                    <!--a href="https://jigsaw.w3.org/css-validator/validator?uri=https://aroug.eu/correlimages/index.php"> <img style="border:0;width:88px;height:31px" src="../vcss.gif" alt="CSS Valide !" /> </a-->
                </div>
            </div>
        </div>
        <img id="img1" src="onepoint.png" alt="image1" style="display:none"><img id="img2" src="onepoint.png" alt="image2" style="display:none">
        <canvas id="Cv1" style="display:none"></canvas><canvas id="Cv2" style="display:none"></canvas>
    </body>

    </html>
