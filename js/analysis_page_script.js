var analysis_path="analysis.php?code=";

//////////////////////////////////////////////////////////////
/////// For analysis page:
//////////////////////////////////////////////////////////////

function showProgress() {
    var run_id_code=getUrlVars()["code"];
    var prog=0;
    
//  remember ajax is asynchronous, so only the stuff inside the success: part will be called after retrieving information. If I put something after the statement, it can't use the info from check_progress.php because it is executed before this php script is called
    //alert('before ajax');
    jQuery.ajax({ 
        type:"POST",
        url: "check_progress.php",
        dataType: 'json',
        data: {code: run_id_code},
        success: function (obj) {
            // alert("inside success");
            // alert(obj);
            prog=obj;

            last_line = prog[prog.length-1];
            console.log(last_line)
            nickname = prog[0]

            document.getElementById("nickname_header").innerHTML = nickname.replace(/_/g," ");

            output_array = prog.slice(1,prog.length);
            output_info = ""
            for (var i=0;i < output_array.length; i++) {
                sub_array = output_array[i].split(",");
                output_info += "<p>" + sub_array.slice(2,sub_array.length) + "</p>";
            }

            document.getElementById("plot_info").innerHTML = output_info
            console.log(last_line.indexOf('SUMMARY,DONE'))


            if (last_line.indexOf('SUMMARY,DONE') > -1) {
                document.getElementById("plot_info").innerHTML = "Analysis completed successfully";
                document.getElementById("progress_panel").className = "panel panel-success center";
                check_plot_exists(0,nickname);
            }
            else if (last_line.indexOf("FAIL") > -1) { // SOMETHING FAILED
                document.getElementById("progress_panel").className = "panel panel-danger center";
            }
            else {
                setTimeout(function(){showProgress();},500);
            }
        }
    });
}


function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}
//
//function test() {
//    var run_id_code=getUrlVars()["code"];
//    alert(run_id_code);
//}
//

var content_width = $( window ).width();

function check_plot_exists(counter,nickname) {
    
    var run_id_code=getUrlVars()["code"];
    var plot_url_prefix="user_data/"+run_id_code + "/" + nickname + ".Assemblytics.";
    var summary_table_url="user_data/"+run_id_code + "/" + nickname + ".Assemblytics_structural_variants.summary";
    var variant_preview_url="user_data/"+run_id_code + "/" + nickname + ".variant_preview.txt";
    var assembly_stats_url="user_data/"+run_id_code + "/" + nickname + ".Assemblytics_assembly_stats.txt";
    
    var zip_file_url="user_data/"+run_id_code + "/" + nickname + ".Assemblytics_results.zip";


    var file_to_wait_for=plot_url_prefix + "dotplot.png";
    console.log(nickname)
    
    if (counter>=100) {
        alert("Taking too long to find "+ file_to_wait_for)
    }
    else {
        jQuery.ajax({ 
            url: file_to_wait_for,
            error: function() {
                console.log(counter+1);
                setTimeout(function(){check_plot_exists(counter+1,nickname);},500);
            },
            success: function () {
                document.getElementById("results").style.visibility= 'visible';
                // alert("inside success");
                document.getElementById("landing_for_plot1").innerHTML='<img class="fluidimage" src="' + plot_url_prefix  + "dotplot.png" + ' "/>'; 
                document.getElementById("landing_for_plot2").innerHTML='<img class="fluidimage" src="' + plot_url_prefix  + "Nchart.png" + ' "/>'; 
                document.getElementById("landing_for_plot3").innerHTML='<img class="fluidimage" src="' + plot_url_prefix  + "size_distributions.png" + ' "/>'; 
                document.getElementById("landing_for_plot4").innerHTML='<img class="fluidimage" src="' + plot_url_prefix  + "size_distributions_zoom_structural.png" + ' "/>'; 
                document.getElementById("landing_for_plot5").innerHTML='<img class="fluidimage" src="' + plot_url_prefix  + "size_distributions_zoom.png" + ' "/>';
                document.getElementById("landing_for_plot6").innerHTML='<img class="fluidimage" src="' + plot_url_prefix  + "size_distributions_large_structural.png" + ' "/>';
                

                document.getElementById("landing_for_summary_statistics").innerHTML='<iframe width="' + content_width+ ' " height="930" src="' + summary_table_url + '" frameborder="0"></iframe>';
                document.getElementById("landing_for_variant_file_preview").innerHTML='<div style="overflow-x:scroll; overflow-y:hidden"> <iframe width="1400" height="190" src="' + variant_preview_url + '" frameborder="0"></iframe></div>';
                document.getElementById("landing_for_assembly_stats").innerHTML='<iframe width="' + content_width + ' " height="290" src="' + assembly_stats_url + '" frameborder="0"></iframe>';

                document.getElementById("download_zip").href = zip_file_url

                imageresize();
            }
        });
    }
}


function imageresize() {
    console.log("resizing")

    var size_fraction = 3; // 1 means fit one plot on the page, 3 means fit 3 plots on the page

    var top_padding = 200;
    var side_padding = 0.05;
    var aspect_ratio = 1;
    var height = Math.min(content_width/aspect_ratio*(1-side_padding), $( window ).height()-top_padding)/size_fraction;
    $(".fluidimage").height(height + "px");
    $(".fluidimage").width(height*aspect_ratio + "px");


    //  Fancybox plot zooming
    // http://www.dwuser.com/education/content/click-to-zoom-for-photos-adding-lightbox-effect-to-your-images/
    var addToAll = true;
    var gallery = true;
    var titlePosition = 'inside';
    $(addToAll ? 'img' : 'img.fancybox').each(function(){
        var $this = $(this);
        var title = $this.attr('title');
        var src = $this.attr('data-big') || $this.attr('src');
        var a = $('<a href="#" class="fancybox"></a>').attr('href', src).attr('title', title);
        $this.wrap(a);
    });
    if (gallery)
        $('a.fancybox').attr('rel', 'fancyboxgallery');
    $('a.fancybox').fancybox({
        titlePosition: titlePosition
    });

    $.noConflict();
}


$(document).ready(function() {
    showProgress();
    $(window).bind("resize", function(){//Adjusts image when browser resized
       imageresize();
    });
});


 

// How to execute code after getting info from multiple files:
    //
    //$.when(
    //    $.get(filename_input, function(csvString) {
    //        array_input = $.csv.toArrays(csvString, {onParseValue: $.csv.hooks.castToScalar}); 
    //    }),
    //    $.get(filename_output, function(csvString) {
    //        array_ouput = $.csv.toArrays(csvString, {onParseValue: $.csv.hooks.castToScalar} ); 
    //    })
    //).then(function() {
    //    console.log(array_input)
    //    console.log(typeof array_input)
    //    console.log(array_input.length)
    //    console.log(array_input[0].length)
    //    var diff=[]
    //    for (i=0; i<v_tick_max+2; i++){
    //        v_ticks.push(i);
    //    }
    //});