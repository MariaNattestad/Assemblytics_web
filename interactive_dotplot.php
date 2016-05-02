<!DOCTYPE html>

<html>


<!--    NAVIGATION BAR-->
    <?php include "header.html";?>

<style> 
    .axis path,line {
      stroke:#ccc;
    }
    .background {
      /*fill:#eee;*/
      fill:#ccc;
    }
    .dotplot_canvas {
      fill:#fff;
    }
    
</style>

<script src="js/d3.v3.min.js"></script>

<script>


function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}

var run_id_code=getUrlVars()["code"];

var directory="user_data/" + run_id_code + "/";
var nickname=getUrlVars()["nickname"];

console.log(run_id_code)
console.log(nickname)


//////////  Positions and sizes for drawing  //////////

var w = window,
    d = document,
    e = d.documentElement,
    g = d.getElementsByTagName('body')[0];

var svg_width;
var svg_height;

var top_edge_padding;
var bottom_edge_padding;
var left_edge_padding;
var right_edge_padding;

var dotplot_canvas_width;
var dotplot_canvas_height;


//////////  Drawing/D3 objects  //////////
var svg = null;
var dotplot_container = null;
var dotplot_canvas = null;

var dotplot_ref_axis;
var dotplot_query_axis;

//////////  Scales  //////////
var dotplot_ref_scale = d3.scale.linear();
var dotplot_query_scale = d3.scale.linear();

//////////  Behavior  ///////////
var zoom = null;

//////////  Data  //////////
var coords_data = null;
var ref_chrom_start_positions = {}; // ref_chrom_start_positions["chr1"] = 234793761 // absolute position on the dot plot
var query_chrom_start_positions = {};  // query_chrom_start_positions["JSAC01000015.1"] = 8237493 // absolute position on the dot plot
var ref_chrom_label_data = [];
var query_chrom_label_data = [];


console.log("Starting");
load_data();
responsive_sizing();

function responsive_sizing() {

  top_banner_height = 120;
  svg_width = (w.innerWidth || e.clientWidth || g.clientWidth);//*0.98;
  svg_height = (w.innerHeight || e.clientHeight || g.clientHeight) - top_banner_height; //*0.98 ;

  // console.log(svg_width)

  top_edge_padding = svg_width*0.03;
  bottom_edge_padding = svg_width*0.03;
  left_edge_padding = svg_width*0.03;
  right_edge_padding = svg_width*0.03; 


  d3.selectAll("svg").remove()

  ////////  Create the SVG  ////////
  svg = d3.select("body")
    .append("svg:svg")
    .attr("width", svg_width)
    .attr("height", svg_height);

  svg.append("rect")
          .attr("width",svg_width)
          .attr("height",svg_height)
          .attr("class","background");


  dotplot_canvas_width = svg_width - left_edge_padding - right_edge_padding;
  dotplot_canvas_height = svg_height - top_edge_padding - bottom_edge_padding;

  // Make it into a square
  dotplot_canvas_width = Math.min(dotplot_canvas_height,dotplot_canvas_width);
  dotplot_canvas_height = dotplot_canvas_width;

}

function load_data() {
    // cat <(echo "ref_start,ref_end,query_start,query_end,ref_length,query_length,ref_chrom,query_chrom") <(cat Saccharomyces_cerevisiae_MHAP_assembly.coords.flipped | cut -f 1,2,3,4,8,9,12,13 | tr "\t" "," ) > Saccharomyces_cerevisiae_MHAP_assembly.coords.flipped.csv
    d3.csv(directory + nickname + ".coords.flipped.csv", function(error,coords_input) {
        if (error) throw error;
        
        for (var i=0;i<coords_input.length;i++){
          coords_input[i].ref_start = +coords_input[i].ref_start
          coords_input[i].query_start = +coords_input[i].query_start
          coords_input[i].ref_end = +coords_input[i].ref_end
          coords_input[i].query_end = +coords_input[i].query_end
          coords_input[i].ref_length = +coords_input[i].ref_length
          coords_input[i].query_length = +coords_input[i].query_length
        }
        coords_data = coords_input; // set global variable for accessing this elsewhere
        calculate_positions();
        draw_dotplot();
    });
}

function calculate_positions() {
    console.log("calculate_positions() STARTING");

    // Find the lengths of each chromosome for both query and reference
    var ref_chromosome_lengths = {};
    var query_chromosome_lengths = {};
    for (var i = 0; i < coords_data.length; i++){
        ref_chromosome_lengths[coords_data[i].ref_chrom] = coords_data[i].ref_length;
        query_chromosome_lengths[coords_data[i].query_chrom] = coords_data[i].query_length;
    }

    // Decide on an optimal assignment of query to reference so we can order them nicely
    // TODO

    ///////////////  Calculate the absolute positions of the starts of each chromosome  ///////////////
    // Reference
    ref_chrom_start_positions = {}; // for quick lookup
    ref_chrom_label_data = []; // for drawing chromosome labels
    var cumulative_ref_size = 0;
    for (var chrom in ref_chromosome_lengths){
        ref_chrom_start_positions[chrom] = cumulative_ref_size; 
        ref_chrom_label_data.push({"chrom":chrom,"pos":cumulative_ref_size});
        cumulative_ref_size += ref_chromosome_lengths[chrom]; 
    }
    // Query
    query_chrom_start_positions = {}; // for quick lookup
    query_chrom_label_data = []; // for drawing chromosome labels
    var cumulative_query_size = 0;
    for (var chrom in query_chromosome_lengths){
        query_chrom_start_positions[chrom] = cumulative_query_size; 
        query_chrom_label_data.push({"chrom":chrom,"pos":cumulative_query_size});
        cumulative_query_size += query_chromosome_lengths[chrom]; 
    }
    // Save the total size of the chromosomes to the domain for the dotplot scale
    dotplot_ref_scale.domain([0,cumulative_ref_size]);
    dotplot_query_scale.domain([0,cumulative_query_size]);


    // Annotate each alignment with an abs_ref_start, abs_ref_end, abs_query_start, abs_query_end that can be plugged directly into the dotplot_ref_scale and dotplot_query_scale scales
    for (var i = 0; i < coords_data.length; i++){
        coords_data[i].abs_ref_start = ref_chrom_start_positions[coords_data[i].ref_chrom] + coords_data[i].ref_start;
        coords_data[i].abs_ref_end = ref_chrom_start_positions[coords_data[i].ref_chrom] + coords_data[i].ref_end;
        coords_data[i].abs_query_start = query_chrom_start_positions[coords_data[i].query_chrom] + coords_data[i].query_start;
        coords_data[i].abs_query_end = query_chrom_start_positions[coords_data[i].query_chrom] + coords_data[i].query_end;
    }





    console.log("calculate_positions() DONE");

}

function draw_dotplot() {
    console.log("draw_dotplot");

    //  Create container object (invisible grouping for the canvas but also contains the axes and axis labels)
    dotplot_container = svg.append("g")
        .attr("transform","translate(" + left_edge_padding + "," + top_edge_padding +")");

    //  Create canvas object (invisible grouping for all drawings inside the plot)
    dotplot_canvas = dotplot_container.append("g");

    //  Create rectangle to create a background color
    dotplot_canvas.append("rect")
          .attr("width",dotplot_canvas_width)
          .attr("height",dotplot_canvas_height)
          .attr("class","dotplot_canvas")

    dotplot_ref_scale.range([0,dotplot_canvas_width]); // start at left and plot towards the right
    dotplot_query_scale.range([dotplot_canvas_height,0]); // flipped so we start at the bottom and then plot up

    // Add axes
    dotplot_ref_axis = d3.svg.axis().scale(dotplot_ref_scale).orient("bottom").ticks(5).tickSize(-dotplot_canvas_height,0,0).tickFormat(d3.format("s"));
    dotplot_container.append("g")
        .attr("class","axis")
        .attr("id","ref_axis")
        .attr("transform","translate(" + 0 + "," + dotplot_canvas_height + ")")
        .call(dotplot_ref_axis);

    dotplot_query_axis = d3.svg.axis().scale(dotplot_query_scale).orient("left").ticks(5).tickSize(-dotplot_canvas_width,0,0).tickFormat(d3.format("s"));
    dotplot_container.append("g")
        .attr("class","axis")
        .attr("id","query_axis")
        .attr("transform","translate(" + 0 + "," + 0 + ")")
        .call(dotplot_query_axis);


    zoom = d3.behavior.zoom()
        .x(dotplot_ref_scale)
        .y(dotplot_query_scale)
        .scaleExtent([1,1000])
        .on("zoom",zoomed);

    dotplot_canvas.call(zoom);

    draw_alignments();
    draw_chromosome_labels();
}

function redraw_on_zoom() {
    draw_alignments();
    draw_chromosome_labels();
}

function zoomed() {
    redraw_on_zoom();
    dotplot_container.select("#ref_axis").call(dotplot_ref_axis);
    dotplot_container.select("#query_axis").call(dotplot_query_axis);
}

// d3.select("button").on("click", reset_dotplot);

// function reset_dotplot() {
//     d3.transition().duration(750).tween("zoom", function() {
//         var ix = d3.interpolate(x.domain(), [-width / 2, width / 2]),
//             iy = d3.interpolate(y.domain(), [-height / 2, height / 2]);
//         return function(t) {
//           zoom.x(x.domain(ix(t))).y(y.domain(iy(t)));
//           zoomed();
//         };
//     });
// }

function draw_alignments() {
    dotplot_canvas.selectAll("line.alignment").remove()
    dotplot_canvas.selectAll("line.alignment")
        .data(coords_data)
        .enter()
        .append("line")
            .filter(function(d) { 
                // return (
                //    // ref start or end is inside
                //   (dotplot_ref_scale(d.abs_ref_start) >0 && dotplot_ref_scale(d.abs_ref_start) < dotplot_canvas_width) ||
                //   (dotplot_ref_scale(d.abs_ref_end) >0 && dotplot_ref_scale(d.abs_ref_end) < dotplot_canvas_width) ) 
                //   &&
                //   ( // query start or end is inside
                //   (dotplot_query_scale(d.abs_query_start) >0 && dotplot_query_scale(d.abs_query_start) < dotplot_canvas_height) ||
                //   (dotplot_query_scale(d.abs_query_end) >0 && dotplot_query_scale(d.abs_query_end) < dotplot_canvas_height) 
                // )
                var x1 = dotplot_ref_scale(d.abs_ref_start);
                var x2 = dotplot_ref_scale(d.abs_ref_end);
                var y1 = dotplot_query_scale(d.abs_query_start);
                var y2 = dotplot_query_scale(d.abs_query_end);
                return !((x1 < 0 && x2 < 0)|| (x1 > dotplot_canvas_width && x2 > dotplot_canvas_width) || (y1 < 0 && y2 < 0) || (y1 > dotplot_canvas_height && y2 > dotplot_canvas_height));
              })
            .attr("class","alignment")
            .style("stroke-width",2)
            .style("stroke", "black")
            .attr("fill","none")
            .attr("x1",function(d){
              var x1 = dotplot_ref_scale(d.abs_ref_start);
              var x2 = dotplot_ref_scale(d.abs_ref_end);
              var y1 = dotplot_query_scale(d.abs_query_start);
              var y2 = dotplot_query_scale(d.abs_query_end);
              var tangent = (y2-y1)/(x2-x1);

              if (x1 < 0) { // left wall
                var new_x = 0;
                var new_y = y1 - x1 * tangent;
                if (new_x >= 0 && new_x <= dotplot_canvas_width && new_y >= 0 && new_y <= dotplot_canvas_height) {
                  return new_x;
                }
              }
              if (y1 > dotplot_canvas_height) { // floor
                var new_x = (dotplot_canvas_height-y1)/tangent + x1;
                var new_y = dotplot_canvas_height;
                if (new_x >= 0 && new_x <= dotplot_canvas_width && new_y >= 0 && new_y <= dotplot_canvas_height) {
                  return new_x;
                }
              }
              if (x1 > dotplot_canvas_width) { // right wall
                var new_x = dotplot_canvas_width;
                var new_y = y1+tangent*(dotplot_canvas_width-x1);
                if (new_x >= 0 && new_x <= dotplot_canvas_width && new_y >= 0 && new_y <= dotplot_canvas_height) {
                  return new_x;
                }
              }
              if (y1 < 0) { // ceiling
                var new_y = 0;
                var new_x = x1 + (0-y1)/tangent;
                if (new_x >= 0 && new_x <= dotplot_canvas_width && new_y >= 0 && new_y <= dotplot_canvas_height) {
                  return new_x;
                }
              }
              return x1;

            })
            .attr("y1",function(d){ 
              var x1 = dotplot_ref_scale(d.abs_ref_start);
              var x2 = dotplot_ref_scale(d.abs_ref_end);
              var y1 = dotplot_query_scale(d.abs_query_start);
              var y2 = dotplot_query_scale(d.abs_query_end);
              var tangent = (y2-y1)/(x2-x1);

              if (x1 < 0) { // left wall
                var new_x = 0;
                var new_y = y1 - x1 * tangent;
                if (new_x >= 0 && new_x <= dotplot_canvas_width && new_y >= 0 && new_y <= dotplot_canvas_height) {
                  return new_y;
                }
              }
              if (y1 > dotplot_canvas_height) { // floor
                var new_x = (dotplot_canvas_height-y1)/tangent + x1;
                var new_y = dotplot_canvas_height;
                if (new_x >= 0 && new_x <= dotplot_canvas_width && new_y >= 0 && new_y <= dotplot_canvas_height) {
                  return new_y;
                }
              }
              if (x1 > dotplot_canvas_width) { // right wall
                var new_x = dotplot_canvas_width;
                var new_y = y1+tangent*(dotplot_canvas_width-x1);
                if (new_x >= 0 && new_x <= dotplot_canvas_width && new_y >= 0 && new_y <= dotplot_canvas_height) {
                  return new_y;
                }
              }
              if (y1 < 0) { // ceiling
                var new_y = 0;
                var new_x = x1 + (0-y1)/tangent;
                if (new_x >= 0 && new_x <= dotplot_canvas_width && new_y >= 0 && new_y <= dotplot_canvas_height) {
                  return new_y;
                }
              }
              return y1;
            })



            .attr("x2",function(d){
              var x1 = dotplot_ref_scale(d.abs_ref_start);
              var x2 = dotplot_ref_scale(d.abs_ref_end);
              var y1 = dotplot_query_scale(d.abs_query_start);
              var y2 = dotplot_query_scale(d.abs_query_end);
               var tangent = (y2-y1)/(x2-x1);

              if (x2 < 0) { // left wall
                var new_x = 0;
                var new_y = y1 - x1 * tangent;
                if (new_x >= 0 && new_x <= dotplot_canvas_width && new_y >= 0 && new_y <= dotplot_canvas_height) {
                  return new_x;
                }
              }
              if (y2 > dotplot_canvas_height) { // floor
                var new_x = (dotplot_canvas_height-y1)/tangent + x1;
                var new_y = dotplot_canvas_height;
                if (new_x >= 0 && new_x <= dotplot_canvas_width && new_y >= 0 && new_y <= dotplot_canvas_height) {
                  return new_x;
                }
              }
              if (x2 > dotplot_canvas_width) { // right wall
                var new_x = dotplot_canvas_width;
                var new_y = y1+tangent*(dotplot_canvas_width-x1);
                if (new_x >= 0 && new_x <= dotplot_canvas_width && new_y >= 0 && new_y <= dotplot_canvas_height) {
                  return new_x;
                }
              }
              if (y2 < 0) { // ceiling
                var new_y = 0;
                var new_x = x1 + (0-y1)/tangent;
                if (new_x >= 0 && new_x <= dotplot_canvas_width && new_y >= 0 && new_y <= dotplot_canvas_height) {
                  return new_x;
                }
              }
              return x2;
            })
            .attr("y2",function(d){ 
              var x1 = dotplot_ref_scale(d.abs_ref_start);
              var x2 = dotplot_ref_scale(d.abs_ref_end);
              var y1 = dotplot_query_scale(d.abs_query_start);
              var y2 = dotplot_query_scale(d.abs_query_end);
              var tangent = (y2-y1)/(x2-x1);

              if (x2 < 0) { // left wall
                var new_x = 0;
                var new_y = y1 - x1 * tangent;
                if (new_x >= 0 && new_x <= dotplot_canvas_width && new_y >= 0 && new_y <= dotplot_canvas_height) {
                  return new_y;
                }
              }
              if (y2 > dotplot_canvas_height) { // floor
                var new_x = (dotplot_canvas_height-y1)/tangent + x1;
                var new_y = dotplot_canvas_height;
                if (new_x >= 0 && new_x <= dotplot_canvas_width && new_y >= 0 && new_y <= dotplot_canvas_height) {
                  return new_y;
                }
              }
              if (x2 > dotplot_canvas_width) { // right wall
                var new_x = dotplot_canvas_width;
                var new_y = y1+tangent*(dotplot_canvas_width-x1);
                if (new_x >= 0 && new_x <= dotplot_canvas_width && new_y >= 0 && new_y <= dotplot_canvas_height) {
                  return new_y;
                }
              }
              if (y2 < 0) { // ceiling
                var new_y = 0;
                var new_x = x1 + (0-y1)/tangent;
                if (new_x >= 0 && new_x <= dotplot_canvas_width && new_y >= 0 && new_y <= dotplot_canvas_height) {
                  return new_y;
                }
              }
              return y2;
            })
      //  NOTE: ceiling is 0, floor is dotplot_canvas_height
}

function draw_chromosome_labels() {
    // console.log("draw_chromosome_labels");
    dotplot_canvas.selectAll("line.chromosome").remove()
    dotplot_canvas.selectAll("line.chromosome")
        .data(ref_chrom_label_data)
        .enter()
        .append("line")
            .filter(function(d) {return (dotplot_ref_scale(d.pos) > 0 && dotplot_ref_scale(d.pos) < dotplot_canvas_width)})
                .attr("class","chromosome")
                .style("stroke-width",1)
                .style("stroke", "blue")
                .attr("fill","none")
                .attr("x1",function(d){ return dotplot_ref_scale(d.pos); })
                .attr("y1",0)
                .attr("x2",function(d){ return dotplot_ref_scale(d.pos); })
                .attr("y2",dotplot_canvas_width)

}





window.onresize = resizeWindow;
function resizeWindow()
{
  responsive_sizing();
  draw_dotplot();
}


</script>

