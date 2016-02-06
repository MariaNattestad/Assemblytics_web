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


function check_plot_exists(counter,nickname) {
    
    
    var run_id_code=getUrlVars()["code"];
    var plot_url="user_data/"+run_id_code + "/" + nickname + ".plot";
    var variant_file_url="user_data/"+run_id_code + "/" + nickname + ".ABVC_structural_variants.bed";
    var summary_table_url="user_data/"+run_id_code + "/" + nickname + ".ABVC_structural_variants.summary";

    var file_to_wait_for=plot_url + ".2.png";
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
                document.getElementById("landing_for_plot1").innerHTML='<img class="fluidimage" src="' + plot_url  + ".1.png" + ' "/>'; 
                document.getElementById("landing_for_plot2").innerHTML='<img class="fluidimage" src="' + plot_url  + ".2.png" + ' "/>'; 
                document.getElementById("landing_for_plot1_details").innerHTML='<iframe  width="600" height="830" src="' + summary_table_url + '" frameborder="0"></iframe>';

                document.getElementById("down_txt_1").href = variant_file_url
                document.getElementById("down_img_1").href = plot_url
                imageresize();
            }
        });
    }
}


//////////////////////////////////

// $(window).resize(function() {
//     // if(this.resizeTO) clearTimeout(this.resizeTO);
//     // this.resizeTO = setTimeout(function() {
//     //     $(this).trigger('resizeEnd');
//     // }, 500);
//     console.log("resizing")
// });

// //redraw graph when window resize is completed  
// $(window).on('resizeEnd', function() {
//     console.log("resize end")
//     document.getElementById("landing_for_plot1").height = $(window).height;
// });
function imageresize() {
    console.log("resizing")
    var top_padding = 150;
    var side_padding = 0.05;
    var aspect_ratio = 1;
    var height = Math.min($( window ).width()/aspect_ratio*(1-side_padding), $( window ).height()-top_padding);
    $(".fluidimage").height(height + "px");
    $(".fluidimage").width(height*aspect_ratio + "px");
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